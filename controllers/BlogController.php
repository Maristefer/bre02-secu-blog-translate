<?php
/**
 * @author : Gaellan
 * @link : https://github.com/Gaellan
 */


class BlogController extends AbstractController
{
     public function __construct()
    {
        $lang = $_SESSION["lang"];

        parent::__construct("blog", $lang);
    }

    public function getTranslator() : Translator
    {
        return $this->translator;
    }
    
    public function home() : void
    {
        $pm = new PostManager();
        $cm = new CategoryManager();

        $posts = $pm->findLatest();
        $categories= $cm->findAll();

        $this->render("home", [ "posts" => $posts, "categories" => $categories]);
    }

    public function category(string $categoryId) : void
    {
        $cm = new CategoryManager();

        $category = $cm->findOne(intval($categoryId));


        if($category !== null)
        {
            $pm = new PostManager();
            $posts = $pm->findByCategory($category->getId());
            $categories = $cm->findAll();

            $this->render("category", ["category" => $category, "posts" => $posts, "categories" => $categories]);
        }
        else
        {
            $this->redirect("index.php");
        }
    }

    public function post(string $postId) : void
    {
        $pm = new PostManager();
        $cm = new CategoryManager();
        $com = new CommentManager();

        $post = $pm->findOne(intval($postId));
        $categories = $cm->findAll();
        $comments = $com->findByPost(intval($postId));

        if($post !== null)
        {
            $this->render("post", [
                "post" => $post,
                "categories" => $categories,
                "comments" => $comments
            ]);
        }
        else
        {
            $this->redirect("index.php");
        }
    }

    public function checkComment() : void
    {
        if(isset($_POST["csrf-token"]) && isset($_POST["content"]) && isset($_POST["post-id"]) && isset($_SESSION["user"]))
        {
            $tokenManager = new CSRFTokenManager();

            if($tokenManager->validateCSRFToken($_POST["csrf-token"]))
            {
                $um = new UserManager();
                $pm = new PostManager();
                $cm = new CommentManager();

                $post = $pm->findOne(intval($_POST["post-id"]));
                $user = $um->findOne($_SESSION["user"]);
                $comment = new Comment(htmlspecialchars($_POST["content"]), $user, $post);
                $cm->create($comment);
            }
        }
        $this->redirect("index.php?route=post&post_id={$_POST["post-id"]}");
    }
    
    public function switchLang()
    {
        if($_SESSION["lang"] === "fr")
        {
            $_SESSION["lang"] = "en";
        }
        else
        {
            $_SESSION["lang"] = "fr";
        }

        $this->redirect("index.php");
    }
}