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

    public function ReservationAction()
    {
        return $this->render('OCPlatformBundle:Hypair:tarif.html.twig');
    }

    public function ProposAction()
    {
        return $this->render('OCPlatformBundle:Hypair:propos.html.twig');
    }

    public function VideoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $videoRepository = $em->getRepository('OCPlatformBundle:Video');

        $listVideo = $videoRepository->findAll();

        $liste = array();

        foreach ($listVideo as $list) {
            $title =  $list->getAlt();
            $date = $list->getDate()->format('d/m/Y');
            $url = $list->getUrl();

            $liste[] = array (
                'date' => $date,
                'title' => $title,
                'url' => $url
            );

        }

        return $this->render(
            'OCPlatformBundle:Hypair:video.html.twig',
            array(
                'video' => $videoRepository,
                'list' => $liste,
            )
        );
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
