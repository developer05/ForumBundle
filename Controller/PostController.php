<?php

namespace Bundle\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bundle\ForumBundle\DAO\Topic;
use Bundle\ForumBundle\DAO\Post;

class PostController extends Controller
{
    public function newAction($topicId)
    {
        $topic = $this['forum.topic_repository']->findOneById($topicId);
        if (!$topic) {
            throw new NotFoundHttpException('The topic does not exist.');
        }

        $user = $this['doctrine_user.auth']->getUser();
        if (!$user) {
            throw new NotFoundHttpException('A user must be logged in.');
        }

        $form = $this->createForm('forum_post_new', $topic);

        return $this->render('ForumBundle:Post:new.'.$this->getRenderer(), array(
            'form' => $form,
            'topic' => $topic,
            'user' => $user
        ));
    }

    public function createAction($topicId)
    {
        $topic = $this['forum.topic_repository']->findOneById($topicId);
        if (!$topic) {
            throw new NotFoundHttpException('The topic does not exist.');
        }

        $user = $this['doctrine_user.auth']->getUser();
        if (!$user) {
            throw new NotFoundHttpException('A user must be logged in.');
        }

        $form = $this->createForm('forum_post_new', $topic);
        $form->bind($this['request']->request->get($form->getName()));

        if(!$form->isValid()) {
            return $this->render('ForumBundle:Post:new.'.$this->getRenderer(), array(
                'form' => $form,
                'topic' => $topic,
                'user' => $user
            ));
        }

        $post = $form->getData();
        $post->setAuthor($user);
        $this->savePost($post);

        $this['session']->setFlash('forum_post_create/success', true);
        $url = $this['templating.helper.forum']->urlForPost($post);

        return $this->redirect($url);
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('forum.template.renderer');
    }

    /**
     * Create a PostForm instance and returns it
     *
     * @param string $name
     * @param Topic $topic
     * @return Bundle\ForumBundle\Form\PostForm
     */
    protected function createForm($name, Topic $topic)
    {
        $formClass = $this->container->getParameter('forum.post_form.class');
        $postClass = $this['forum.post_repository']->getObjectClass();
        $post = new $postClass();
        $post->setTopic($topic);

        return new $formClass($name, $post, $this['validator']);
    }

    /**
     * Save a post in database
     *
     * @param Post $post
     * @return null
     **/
    public function savePost(Post $post)
    {
        $objectManager = $this['forum.post_repository']->getObjectManager();
        $objectManager->persist($post);
        $objectManager->flush();
    }

}
