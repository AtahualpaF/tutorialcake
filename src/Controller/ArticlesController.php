<?php
// src/Controller/ArticlesController.php

namespace App\Controller;

use App\Controller\AppController;

class ArticlesController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Paginator');
        $this->loadComponent('Flash'); // Include the FlashComponent
        // Add this line to check authentication result and lock your site
        $this->loadComponent('Authentication.Authentication');
    }

    public function index()
    {
        
        // View, index and tags actions are public methods
        // and don't require authorization checks.
        $this->Authorization->skipAuthorization();

        $this->loadComponent('Paginator');
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));

        
    }

    public function foobar()
    {
        $this->loadComponent('Paginator');
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    public function view($slug = null)
    {
        
        // View, index and tags actions are public methods
        // and don't require authorization checks.
        $this->Authorization->skipAuthorization();

        // Update retrieving tags with contain()
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain(['Tags','Users'])
            //->contain('Users')
            ->firstOrFail();
        $this->set(compact('article'));


    }

    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        $this->Authorization->authorize($article);

        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // Hardcoding the user_id is temporary, and will be removed later
            // when we build authentication out.
            // Changed: Set the user_id from the current user.
            $article->user_id = $this->request->getAttribute('identity')->getIdentifier();


            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }

        // Get a list of tags.
        $tags = $this->Articles->Tags->find('list')->all();

        // Set tags to the view context
        //$this->set('tags', $tags);
        //$this->set('article', $article);
        $this->set(compact('article', 'tags'));
    }


    public function edit($slug)
    {
        // Update this line
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();

        $this->Authorization->authorize($article);

        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                // Added: Disable modification of user_id.
                'accessibleFields' => ['user_id' => false]
            ]);

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        // Get a list of tags.
        $tags = $this->Articles->Tags->find('list')->all();

        // Set tags to the view context
        //$this->set('tags', $tags);
        //$this->set('article', $article);
        $this->set(compact('article', 'tags'));
    }

    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);
    
        $article = $this->Articles->findBySlug($slug)->firstOrFail();

        $this->Authorization->authorize($article);

        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
    }



    public function tags()
    {
        
         // View, index and tags actions are public methods
        // and don't require authorization checks.
        $this->Authorization->skipAuthorization();

        // The 'pass' key is provided by CakePHP and contains all
        // the passed URL path segments in the request.
        $tags = $this->request->getParam('pass');

       
        // Use the ArticlesTable to find tagged articles.
        $articles = $this->Articles->find('tagged', [
                'tags' => $tags
            ])
            ->all();

        // Pass variables into the view template context.
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }

    //Disponibilizar para Articles o Users, tentativa.
    // public function users()
    // {
    //     $this->Authorization->skipAuthorization();

    //     // The 'pass' key is provided by CakePHP and contains all
    //     // the passed URL path segments in the request.
    //     $users = $this->request->getParam('pass');

    //     // Use the ArticlesTable to find tagged articles.
    //     $articles = $this->Articles->find('used', [
    //             'users' => $users
    //         ])
    //         ->all();

    //     // Pass variables into the view template context.
    //     $this->set([
    //         'articles' => $articles,
    //         'users' => $users
    //     ]);
    // }

}

