<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 16/04/17
 * Time: 14:02
 */

namespace OC\PlatformBundle\Controller;


use DateTime;
use OC\PlatformBundle\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddVideoController extends Controller
{
    public function addAction(Request $request)
    {
        // On crée un objet Advert
        $oVideo = new Video();

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $oVideo);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('date',    TextType::class)
            ->add('url',     TextType::class)
            ->add('alt',     TextType::class)
            ->add('save',    SubmitType::class)
        ;
        // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $oVideo->setDate(DateTime::createFromFormat("d/m/Y", $oVideo->getDate()));

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($oVideo);
            $em->flush();

            return $this->render('OCPlatformBundle:Hypair:accueil.html.twig');
        }

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('OCPlatformBundle:admin:addVideo.html.twig', array(
            'form' => $form->createView(),
        ));


    }
}