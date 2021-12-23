<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Categoria;
use App\Form\ProductoType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Bundle\FrameworkBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StandardController extends AbstractController
{  

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        //Creando el formulario
        $producto = new Producto();
        $form = $this -> createForm(ProductoType::class, $producto);
        //

        $Num1 = 100;
        $Num2 = 1;
        $Suma = $Num1 + $Num2;
        $nombres = 'diego, juan, pepito, miguel, LUIS, pedro';
        //Parte de formulario
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $producto = $form->getData();
            $em-> persist($producto);
            $em-> flush();

            return $this->redirectToRoute('index');
        }
        //Formulario

        return $this->render('standard/index.html.twig',
            array(
                'SumaEntreNumeroUnoYNumeroDos'=>$Suma,
                "Num1" => $Num1,
                "Num2" => $Num2,
                "nombres" => $nombres,
                "form" => $form->createView()
                )
        );
       
    }

    #[Route('/pagina2/{nombre}', name: 'pagina2')]
    public function pagina2(Request $request, $nombre):Response{
        //Formulario no relacionado con ninguna entidad
        $form = $this->createFormBuilder()
            ->add('nombre')
            ->add('codigo')
            ->add('categoria', EntityType:: class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre'
            ])
            ->add('Enviar', SubmitType::class)
            ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this -> getDoctrine() -> getManager();
                $data = $form -> getData();
                $producto = new Producto($data['nombre'], $data['codigo']);
                $producto -> setCategoria($data['categoria']);
                $em-> persist($producto);
                $em-> flush();

                return $this->redirectToRoute('pagina2', ['nombre' => 'Guardado exitoso']);
            }
        ;
        //

        return $this->render('standard/pagina2.html.twig', array('parametro1' => $nombre, 'form' => $form-> createView()));
    }

    #[Route('/PersistirDatos', name: 'Persistir')]
    public function PersistirDatos():Response{       
        $entityManager = $this-> getDoctrine()-> getManager();
        $categoria = new Categoria("Tecnología");
        $producto = new Producto('TV SONY 32 pulgadas','TV-01');
        $producto -> setCategoria($categoria);

        $entityManager->persist($producto);
        $entityManager->flush();

        return $this->render('standard/success.html.twig', [   
                   
        ]);
    }

    #[Route('/Busquedas/{idProducto}', name: 'Busquedas')]
    public function Busquedas($idProducto):Response{
        $em = $this->getDoctrine()->getManager();
        $producto = $em -> getRepository(Producto::class)->find(1);
        $producto2 = $em -> getRepository(Producto::class)->findOneBy(['codigo'=>'T-01']);
        $producto3 = $em -> getRepository(Producto::class)->findBy(['categoria'=>'1']);
        $productos = $em -> getRepository(Producto::class)->findAll();

        //===================================================================//
        $productoRepository = $em ->getRepository(Producto::class)->BuscarProductoPorId($idProducto);

        return $this->render('standard/busquedas.html.twig', array(
            'find'=>$producto, 
            'findOneBy'=> $producto2,
            'findBy'=> $producto3,
            'findAll'=> $productos,
            'BuscarProductoPorId' => $productoRepository
        ));
    }
}
