<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 29/01/17
 * Time: 15:48
 */

namespace OC\PlatformBundle\Controller;


use Doctrine\Common\Util\Debug;
use OC\PlatformBundle\Entity\hypair;
use OC\PlatformBundle\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class AddPhotoController extends Controller
{
    public function addAction(Request $request)
    {
        $event = "add";
        // On crée un objet Advert
        $oPhoto = new hypair();

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $oPhoto);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('date',    TextType::class)
            ->add('title',   TextType::class)
            ->add('file',     FileType::class)
            ->add('save',    SubmitType::class)
        ;
        // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $file = $oPhoto->getFile();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('photo'),
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $oPhoto->setFile($fileName);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($oPhoto);
            $em->flush();

            return $this->render('OCPlatformBundle:Hypair:photo.html.twig');
        }

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('OCPlatformBundle:admin:addPhoto.html.twig', array(
            'form' => $form->createView(),
        ));


    }


}