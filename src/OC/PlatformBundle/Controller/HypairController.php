<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 08/12/16
 * Time: 16:37
 */

namespace OC\PlatformBundle\Controller;


use OC\PlatformBundle\Entity\Admin;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Form\AdminType;
use OC\PlatformBundle\Form\AdvertForm;
use OC\PlatformBundle\Form\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class HypairController extends Controller
{

public function IndexAction()
    {
        return $this->render('OCPlatformBundle:Hypair:accueil.html.twig');
    }

    public function PhotoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $hypairRepository = $em->getRepository('OCPlatformBundle:hypair');

        $listHypair = $hypairRepository->findAll();

        $liste = array();

        foreach ($listHypair as $list) {
            $title =  $list->getTitle();
            $date = $list->getDate();
            $image = $list->getFile();
            $liste[] = array (
                'date' => $date,
                'title' => $title,
                'image' => $image
            );

        }

        return $this->render(
            'OCPlatformBundle:Hypair:photo.html.twig',
            array(
                'hypair' => $hypairRepository,
                'list' => $liste,
            )
            );
    }


    public function AdddAction(Request $request)
    {
        $advert = new Image();

        $form = $this->createForm(ImageType::class, $advert);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $advert->getImage()->upload();
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Fichier bien enregistrée.');

            return $this->redirectToRoute('ajout_page');
        }

        return $this->render('OCPlatformBundle:admin:ajout.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function addAction(Request $request)
    {
        // Création de l'entité Advert
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

        // Création de l'entité Image
        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        // On lie l'image à l'annonce
        $advert->setImage($image);

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Étape 1 : On « persiste » l'entité
        $em->persist($advert);

        // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
        // on devrait persister à la main l'entité $image
        // $em->persist($image);

        // Étape 2 : On déclenche l'enregistrement
        $em->flush();

        // Reste de la méthode qu'on avait déjà écrit
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));
    }

    public function AdministrationAction(Request $request)
    {
        $event = "add";
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('ajout_photo_page', array($event));
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($advert);
//            $em->flush();
            $id= $admin->getCompte();
            return $this->redirectToRoute('ajout_photo_page', array($event));
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('OCPlatformBundle:admin:administration.html.twig', array(
            'form' => $form->createView(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }

    public function addPhotoAction(Request $request)
    {
        $event = "add";
        $advert = new Advert();
        $form = $this->createForm(AdvertForm::class, $advert);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $event = "success";
            return $this->redirectToRoute('ajout_photo_page', array($event));
        } elseif ($request->isMethod('POST')){
            $event = "failed";
            return $this->redirectToRoute('ajout_photo_page', array($event));
        }

        return $this->render('OCPlatformBundle:admin:addPhoto.html.twig', array(
            'form'  => $form->createView(),
            'event' => $event,
        ));
    }
}
