<?php
// src/Model/Entity/Article.php
namespace App\Model\Entity;

use Cake\ORM\Entity;

// add this use statement right below the namespace declaration to import
// the Collection class
use Cake\Collection\Collection;

class Article extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'slug' => false,
        'tag_string' => true
        //'user_string' => true
    ];

    protected function _getTagString()
    {
        if (isset($this->_fields['tag_string'])) {
            return $this->_fields['tag_string'];
        }
        if (empty($this->tags)) {
            return '';
        }
        $tags = new Collection($this->tags);
        $str = $tags->reduce(function ($string, $tag) {
            return $string . $tag->title . ', ';
        }, '');
        return trim($str, ', ');
    }

    //Tentativa para usar o User no Article
    // protected function _getUserString()
    // {
    //     if (isset($this->_fields['user_string'])) {
    //         return $this->_fields['user_string'];
    //     }
    //     if (empty($this->users)) {
    //         return '';
    //     }
    //     $users = new Collection($this->users);
    //     $str = $users->reduce(function ($string, $user) {
    //         return $string . $user->email . ', ';
    //     }, '');
    //     return trim($str, ', ');
    // }


}

