<!-- File: templates/Articles/view.php -->

<h1><?= h($article->title) ?></h1>
<p><?= h($article->body) ?></p>



<!-- Add the following line -->
<p><b>Tags:</b> <?= h($article->tag_string) ?></p>

<p><small>Created: <?= $article->created->format(DATE_RFC850) ?></small></p>
<!-- Para utilizar um valor de uma outra tabela, basta colocar no contain e depois fazer a tabela (com belongTo por ex) e seguir como no codigo abaixo 'article->user->email' -->
<!-- 'article' é o local onde se está, 'user' está assossiado, e o 'email' é o atributo do user que se quer puxar o valor -->
<p><small>Autor: <?= $article->user->email ?></small></p>
<p><?= $this->Html->link('Edit', ['action' => 'edit', $article->slug]) ?></p>
