<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface as ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
         @Route("/blog", name="blog")
     */

    public function index(ArticleRepository $repo, PaginatorInterface $paginator, Request $request)
    {
        // $repo = $this->getDoctrine()->getRepository(Article::class);
         //$article = $repo->findOneByTitle('Titre de l\'article');

       $articles = $repo->findAllArticle()->getResult();

       $query = $repo->findAllArticle();

        // $articles = $repo->findAll();
         $paginationArticle = $paginator->paginate(
                                $query, /* query NOT result */
                                $request->query->getInt('page', 1), /*page number*/
                                2 /*limit per page*/
                                );

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',            
            'pagination_article' => $paginationArticle
        ]);
    }
  
    /** 
        @Route("/", name="home")
    */
    public function home(): Response 
    {

        return $this->render('blog/home.html.twig', [
            'name' => 'Said',
            'age' => 36
            ]);

    }

    /**
        @Route("/blog/new", name = "blog_create")
        @Route("/blog/edit/{id}", name = "blog_edit")
    */

     public function formEditingArticle(Article $article = null, Request $request, ObjectManager $manager)
     {

      /* Old method

        $title = $request->request->get('title');

        return $this->render('blog/create.html.twig', [
            'title' => $title
        ]);
    */
            
            if(!$article) $article = new Article();

           /* 

        *** OLD METHOD ****

           
           $form = $this->createFormBuilder($article)
                ->add('title')
                ->add('content')
                ->add('image', TextType::class, [
                    'attr' => [
                        'placeholder' => "Votre image url ici",
                        'title' => "Image url",
                        'class' => "form-control"
                    ]
                ])
                ->add('Save', SubmitType::class, [
                    'label' => "Enregistrer"
                ])
                ->getForm()
                ;
                
        */

                //FROM ArticleType created by CLI
                $form = $this->createForm(ArticleType::class, $article);

                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()){

                    if(!$article->getCreatedAt()) $article->setCreatedAt(new \DateTime());

                    $manager->persist($article);

                    $manager->flush();

                    return $this->redirectToRoute('show_article', ['id' => $article->getId()]);

                }     

            return $this->render('blog/create.html.twig', [
                'formArticle' => $form->createView(),
                'editMode' => $article->getId() == true
            ]);                

    }
    
    /**
        @Route("/blog/{id}", name="show_article")
     */

    public function show(Article $article, Request $request, ObjectManager $manager): Response
    {

        $comment = new Comment();
       
        $formComment = $this->createForm(CommentType::class, $comment)
                             ->add('Save', SubmitType::class, [
                                    'label' => "Ajouter"
                                ])
                                        ;
        
        $formComment->handleRequest($request);
        
        if($formComment->isSubmitted() && $formComment->isValid()){

            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager->persist($comment);

            $manager->flush();
            
            return $this->redirectToRoute('show_article', ['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig', [
            "article" => $article,
            "formComment" => $formComment->createView() 
        ]);
    
    }
    

/* Other method : 

     public function show(ArticleRepository $repo, $id): Response
    {

       // $repo = $this->getDoctrine()->getRepository(Article::class);
       
        $article = $repo->find($id);

        return $this->render('blog/show.html.twig', [
            "article" => $article
        ]);
    
    }
*/

   
}

