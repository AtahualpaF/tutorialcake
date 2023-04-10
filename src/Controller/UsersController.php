<?php
declare(strict_types=1);

namespace App\Controller;

use Authorization\IdentityInterface;
//use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception;


/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // In the add, login, and logout methods
        $this->Authorization->skipAuthorization();
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        
        // In the add, login, and logout methods
        //$this->Authorization->skipAuthorization();  
        
       
        
        $user = $this->Users->get($id, [
            'contain' => ['Articles'],
        ]);
        $resource = $user;
        $this->Authorization->authorize($resource);
        $this->set(compact('user'));

    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // In the add, login, and logout methods
        $this->Authorization->skipAuthorization();        
        
        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }



        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        //$resource = $user;
        //$this->Authorization->skipAuthorization();
        $this->Authorization->authorize($user);

        if ($this->request->is(['patch', 'post', 'put'])) {
            //$user = $this->Users->patchEntity($user, $this->request->getData());
            
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                // Added: Disable modification of user_id.
                'accessibleFields' => ['id' => false]
            ]);
            
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null /*, $administrador = null*/ /*, IdentityInterface $useraut*/)
    {
                   
        //$this->Authorization->skipAuthorization();
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        
        //$resource = $user;
        //$this->Authorization->authorize($user);

        //$admin = $this->Authentication->getIdentity();
        //$admin = $request->getAttribute('administrador');
        //$admin = $this->Users->get($administrador);
        //$admin = $Users->$administrador;
        //if ($useraut->administrador){ //;
        //if ($this->Authorization->authorize($user)){ //;
        
        try{
            $this->Authorization->authorize($user);  

            if ($this->Users->delete($user)) {
                $this->Flash->success(__('The user has been deleted.'));
            } else {
                $this->Flash->error(__('The user could not be deleted. Please, try again.'));
            }

        } catch(ForbiddenException $ex){
            $this->Flash->error(message: 'Você não tem autorização para deletar usuário');
        }
        
            

        // }
        // else {
        //     $this->Flash->set('Você não tem autorização para deletar usuário');
        // }

        return $this->redirect(['action' => 'index']);
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        //$this->Authentication->addUnauthenticatedActions(['login']);
        $this->Authentication->addUnauthenticatedActions(['login', 'add']);
    }

    public function login()
    {

        // In the add, login, and logout methods
        $this->Authorization->skipAuthorization();

        $this->request->allowMethod(['get', 'post']);

        
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result && $result->isValid()) {
            // redirect to /articles after login success
            $redirect = $this->request->getQuery('redirect', [
                'controller' => 'Articles',
                'action' => 'index',
            ]);

            return $this->redirect($redirect);
        }
        // display error if user submitted and authentication failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid username or password'));
        }
    }

    // in src/Controller/UsersController.php
    public function logout()
    {
        
        // In the add, login, and logout methods
        $this->Authorization->skipAuthorization();

        $result = $this->Authentication->getResult();
        
        // regardless of POST or GET, redirect if user is logged in
        if ($result && $result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }


}
