<?php

require_once(ROOT_DIR . '/config.php');
require_once(ROOT_DIR . '/PhpHelper.php');
require_once(ROOT_DIR . '/model/UserManager.php');
require_once(ROOT_DIR . '/model/CatManager.php');
require_once(ROOT_DIR . '/model/SnippetManager.php');
require_once(ROOT_DIR . '/config/MyPdo.php');
require_once(ROOT_DIR . '/service/SnippetService.php');

class SnippetCtrl
{
    private $_userManager;
    private $_catManager;
    private $_snippetManager;
    private $_snippetService;

    public function __construct()
    {
        $db = new MyPdo();
        $this->_userManager = new UserManager($db);
        $this->_catManager = new CatManager($db);
        $this->_snippetManager = new SnippetManager($db);
        $this->_snippetService = new SnippetService($db);
    }

    public function getOne($id) {
        $cats = $this->_catManager->getListCats();
        if (isset($_GET ['cat'])) {
            $snippets = $this->_snippetManager->getListSnippetsByCat($_GET ['cat']);
        } else {
            $snippets = $this->_snippetManager->getListSnippets();
        }
        $snippet = $this->_snippetService->findById($id);
        require(ROOT_DIR . '/view/oneSnippetView.php');
    }
    public function add() {
        if (isset($_POST['validate'])) {
            $snippet = new Snippet();
            $snippet->setTitle($_POST['title']);
            $snippet->setLanguage($_POST['language']);
            $snippet->setCode(htmlentities($_POST['code']));
            $snippet->setDateCrea(date("Y-m-d H:i:s"));
            $snippet->setComment($_POST['comment']);
            $snippet->setRequirement($_POST['requirement']);
            $snippet->setUserId($_POST['userId']);
            $snippet = $this->_snippetManager->addSnippet($snippet);
            header('location: ?action=oneSnippet&id=' . $snippet->getSnippetId());
        } else {
            $users = $this->_userManager->getListUsers();
            $cats = $this->_catManager->getListCats();
            $snippets = $this->_snippetManager->getListSnippets();
            require(ROOT_DIR . '/view/addUpdSnippetView.php');
        }
    }
    public function delete($id)
    {
        $deleted = $this->_snippetManager->delSnippet($id);
        $cats = $this->_catManager->getListCats();
        $snippets = $this->_snippetManager->getListSnippets();
        $snippet = $this->_snippetService->findLast();
        require(ROOT_DIR . '/view/oneSnippetView.php');
    }
    public function update($id) {
        if (isset($_POST['validate'])) {
            $snippet = new Snippet();
            $snippet->setSnippetId($_POST['snippetId']);
            $snippet->setTitle($_POST['title']);
            $snippet->setLanguage($_POST['language']);
            $snippet->setCode(htmlentities($_POST['code']));
            $snippet->setComment(htmlentities($_POST['comment']));
            $snippet->setRequirement(htmlentities($_POST['requirement']));
            $snippet->setUserId($_POST['userId']);
            $snippet = $this->_snippetManager->updSnippet($snippet);
            if (!is_null($snippet)) {
                header('location: ?action=oneSnippet&id=' . $snippet->getSnippetId());
            } else {
                PhpHelper::debug($_POST);
            }

        } else {
            $users = $this->_userManager->getListUsers();
            $cats = $this->_catManager->getListCats();
            $snippets = $this->_snippetManager->getListSnippets();
            $snippet = $this->_snippetManager->getOneSnippet($id);
            require(ROOT_DIR . '/view/addUpdSnippetView.php');
        }
    }
    public function getLast() {
        $cats = $this->_catManager->getListCats();
        if (isset($_GET ['cat'])) {
            $snippets = $this->_snippetManager->getListSnippetsByCat($_GET ['cat']);
            $snippet = $this->_snippetService->findLastByCat($_GET ['cat']);
        } else {
            $snippets = $this->_snippetManager->getListSnippets();
            $snippet = $this->_snippetService->findLast();
        }
        require(ROOT_DIR . '/view/oneSnippetView.php');
    }
}
