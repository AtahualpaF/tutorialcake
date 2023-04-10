<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

// the Text class
use Cake\Utility\Text;
// the EventInterface class
use Cake\Event\EventInterface;

// add this use statement right below the namespace declaration to import
// the Validator class
use Cake\Validation\Validator;

// add this use statement right below the namespace declaration to import
// the Query class
use Cake\ORM\Query;

// Add the following method.

class ArticlesTable extends Table
{
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        // Change this line
        $this->belongsToMany('Tags', [
            'joinTable' => 'articles_tags',
            'dependent' => true
        ]);

        $this->belongsTo('Users');
    }

    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if ($entity->tag_string) {
            $entity->tags = $this->_buildTags($entity->tag_string);
        }

        if ($entity->user_string) {
            $entity->users = $this->_buildUsers($entity->user_string);
        }
        
        if ($entity->isNew() && !$entity->slug) {
        $sluggedTitle = Text::slug($entity->title);
        // trim slug to maximum length defined in schema
        $entity->slug = substr($sluggedTitle, 0, 191);
        }
    } 
    
    protected function _buildTags($tagString)
    {
        // Trim tags
        $newTags = array_map('trim', explode(',', $tagString));
        // Remove all empty tags
        $newTags = array_filter($newTags);
        // Reduce duplicated tags
        $newTags = array_unique($newTags);

        $out = [];
        $tags = $this->Tags->find()
            ->where(['Tags.title IN' => $newTags])
            ->all();

        // Remove existing tags from the list of new tags.
        foreach ($tags->extract('title') as $existing) {
            $index = array_search($existing, $newTags);
            if ($index !== false) {
                unset($newTags[$index]);
            }
        }
        // Add existing tags.
        foreach ($tags as $tag) {
            $out[] = $tag;
        }
        // Add new tags.
        foreach ($newTags as $tag) {
            $out[] = $this->Tags->newEntity(['title' => $tag]);
        }
        return $out;
    }

    //Tentativa de tornar o User visivel no Article
    // protected function _buildUsers($userString)
    // {
    //     // Trim tags
    //     $newTags = array_map('trim', explode(',', $userString));
    //     // Remove all empty tags
    //     $newTags = array_filter($newTags);
    //     // Reduce duplicated tags
    //     $newTags = array_unique($newTags);

    //     $out = [];
    //     $users = $this->Users->find()
    //         ->where(['Tags.title IN' => $newTags])
    //         ->all();

    //     // Remove existing tags from the list of new tags.
    //     foreach ($users->extract('title') as $existing) {
    //         $index = array_search($existing, $newUsers);
    //         if ($index !== false) {
    //             unset($newUsers[$index]);
    //         }
    //     }
    //     // Add existing tags.
    //     foreach ($users as $user) {
    //         $out[] = $user;
    //     }
    //     // Add new tags.
    //     foreach ($newUsers as $user) {
    //         $out[] = $this->Users->newEntity(['title' => $user]);
    //     }
    //     return $out;
    // }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('title')
            ->minLength('title', 10)
            ->maxLength('title', 255)

            ->notEmptyString('body')
            ->minLength('body', 10);

        return $validator;
    }

    // The $query argument is a query builder instance.
    // The $options array will contain the 'tags' option we passed
    // to find('tagged') in our controller action.
    public function findTagged(Query $query, array $options)
    {
        $columns = [
            'Articles.id', 'Articles.user_id', 'Articles.title',
            'Articles.body', 'Articles.published', 'Articles.created',
            'Articles.slug',
        ];

        $query = $query
            ->select($columns)
            ->distinct($columns);

        if (empty($options['tags'])) {
            // If there are no tags provided, find articles that have no tags.
            $query->leftJoinWith('Tags')
                ->where(['Tags.title IS' => null]);
        } else {
            // Find articles that have one or more of the provided tags.
            $query->innerJoinWith('Tags')
                ->where(['Tags.title IN' => $options['tags']]);
        }

        return $query->group(['Articles.id']);
    }

    
    // public function findUsed(Query $query, array $options)
    // {
    //     $columns = [
    //         'Articles.id', 'Articles.user_id', 'Articles.title',
    //         'Articles.body', 'Articles.published', 'Articles.created',
    //         'Articles.slug',
    //     ];

    //     $query = $query
    //         ->select($columns)
    //         ->distinct($columns);

    //     // if (empty($options['users'])) {
    //     //     // If there are no tags provided, find articles that have no tags.
    //     //     $query->leftJoinWith('Users')
    //     //         ->where(['Users.title IS' => null]);
    //     // } else {
    //     //     // Find articles that have one or more of the provided tags.
    //     //     $query->innerJoinWith('Users')
    //     //         ->where(['Users.title IN' => $options['users']]);
    //     // }

    //     return $query->group(['Articles.id']);
    // }


}

