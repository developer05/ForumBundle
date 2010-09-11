<ul class="forum_topics_list">
<?php foreach ($topics as $topic): ?>
    <li class="topic">
        <div class="content">
            <a class="subject" href="<?php echo $view['forum']->urlForTopic($topic) ?>"><?php echo $topic->getSubject() ?></a>
        </div>
        <div class="metas">
            <span class="creation">Created <span class="createdAt"><?php echo $view['time']->ago($topic->getCreatedAt()) ?></span> by
            <?php if ($author = $topic->getAuthor()): ?>
                <a class="author" href="<?php echo $view['forum']->urlForUser($author) ?>"><?php echo $author->getUsername() ?></a>
            <?php else: ?>
                <span class="author">Anonymous</span>
            <?php endif ?>
            </span>
            | <span class="numReplies"><?php echo $topic->getNumReplies() . ' ' . ($topic->getNumReplies() > 1 ? 'replies' : 'reply') ?></span>
            | <a class="category" href="<?php echo $view['forum']->urlForCategory($topic->getCategory()) ?>"><?php echo $topic->getCategory()->getName() ?></a>
        </div>
    </li>
<?php endforeach ?>
</ul>