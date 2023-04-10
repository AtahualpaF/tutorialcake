<!-- File: templates/Articles/index.php -->


<!-- File: templates/Articles/index.php  (edit links added) -->

<h1>Articles</h1>
<div class="users index content">

    <?= $this->Html->link(__('Add Article'), ['action' => 'add'], ['class' => 'button float-right' , 'color' => '#3CB371' ] /*, ['color' => '#3CB371'] */ ) ?>

    <!--<p><?= $this->Html->link("Add Article", ['action' => 'add']) ?></p> -->
    <table>
        <tr>
            <th>Title</th>
            <th>Created</th>
            <th>Action</th>
        </tr>

    <!-- Here's where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($articles as $article):?>
        <tr>
            <td class="actions">
                <?= $this->Html->link($article->title, ['action' => 'view', $article->slug]) ?>
            </td>
            <td>
                <?= $article->created->format(DATE_RFC850) ?>
            </td>
            <td class="actions">
                <?= $this->Html->link('View', ['action' => 'view', $article->slug]) ?>
                <?= $this->Html->link('Edit', ['action' => 'edit', $article->slug]) ?>
                
                <!-- teste para ocultar delete caso não seja admin -->
               
                    
                <?=  $this->Form->postLink(
                    'Delete',
                    ['action' => 'delete', $article->slug] ,
                    ['confirm' => 'Você tem certeza?'])
                ?>
                <!-- teste para ocultar delete caso não seja admin -->
             
            </td>
        </tr>
    <?php endforeach;  ?>

    </table>

    <div class="paginator">
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>

</div>

