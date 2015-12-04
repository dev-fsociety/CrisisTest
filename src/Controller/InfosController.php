<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
/**
 * Infos Controller
 *
 * @property \App\Model\Table\InfosTable $Infos
 */
class InfosController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['view','index']);
    }
    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Crisis', 'Users']
        ];
        $this->set('infos', $this->paginate($this->Infos));
        $this->set('_serialize', ['infos']);
    }

    /**
     * View method
     *
     * @param string|null $id Info id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $info = $this->Infos->get($id, [
            'contain' => ['Crisis', 'Users']
        ]);
        $this->set('info', $info);
        $this->set('_serialize', ['info']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $info = $this->Infos->newEntity();
        if ($this->request->is('post')) {
            $info = $this->Infos->patchEntity($info, $this->request->data);
            if ($this->Infos->save($info)) {
                $this->Flash->success(__('The info has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The info could not be saved. Please, try again.'));
            }
        }
        $Crisis = $this->Infos->Crisis->find('list', ['limit' => 200]);
        $users = $this->Infos->Users->find('list', ['limit' => 200]);
        $this->set(compact('info', 'Crisis', 'users'));
        $this->set('_serialize', ['info']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Info id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $info = $this->Infos->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $info = $this->Infos->patchEntity($info, $this->request->data);
            if ($this->Infos->save($info)) {
                $this->Flash->success(__('The info has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The info could not be saved. Please, try again.'));
            }
        }
        $Crisis = $this->Infos->Crisis->find('list', ['limit' => 200]);
        $users = $this->Infos->Users->find('list', ['limit' => 200]);
        $this->set(compact('info', 'Crisis', 'users'));
        $this->set('_serialize', ['info']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Info id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $info = $this->Infos->get($id);
        if ($this->Infos->delete($info)) {
            $this->Flash->success(__('The info has been deleted.'));
        } else {
            $this->Flash->error(__('The info could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function isAuthorized($user)
    {
        // A logged user can do an action about infos
        if($this->request->action === 'add' && $user['id'] > 0)
        {
            return true;
        }

        if(in_array($this->request->action, ['edit', 'delete']))
        {
            $infoId = (int)$this->request->params['pass'][0];

            if($this->Infos->isOwnedBy($infoId, $user['id']))
            {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }
}
