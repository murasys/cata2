<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Choice;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction()
    {
        $todos = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->findAll();
        // replace this example code with whatever you need
        return $this->render('todo/index.html.twig', array('todos' => $todos));
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new Todo;
        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('priority', ChoiceType::class, array('choices' => array('low' => 'Low', 'high' => 'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn', 'style' => 'margin-bottom:15px')))
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();

            $now = new\DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDuedate($due_date);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            $this->addFlash('notice',
                            'Todo added'
                );
            return $this->redirectToRoute('todo_list');

        }

        // replace this example code with whatever you need
        return $this->render('todo/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/todo/edit/", name="todo_edit1");
     * @Route("/todo/edit/{id}", name="todo_edit");
     */
    public function editAction($id = null, Request $request)
    {
        if(is_null($id)) {
            $this->addFlash('notice',
                'Incorrect request'
            );
            return $this->redirectToRoute("todo_list");
        }
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);
        if(is_null($todo)) {
            $this->addFlash('notice',
                'No row with this id'
            );
            return $this->redirectToRoute("todo_list");
        }
        $now = new\DateTime('now');

        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('priority', ChoiceType::class, array('choices' => array('low' => 'Low', 'high' => 'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Update', 'attr' => array('class' => 'btn', 'style' => 'margin-bottom:15px')))
                ->getForm();
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $name = $form['name']->getData();
                $category = $form['category']->getData();
                $description = $form['description']->getData();
                $priority = $form['priority']->getData();
                $due_date = $form['due_date']->getData();


                //$todo = $form->getData();
                $now = new\DateTime('now');
                $todo->setCreateDate($now);




                $todo = $em->getRepository('AppBundle:Todo')->find($id);

                $todo->setName($name);
                $todo->setCategory($category);
                $todo->setDescription($description);
                $todo->setPriority($priority);
                $todo->setDuedate($due_date);
                $todo->setCreateDate($now);

                $em->flush();

                $this->addFlash('notice',
                    'Todo updated'
                );
                return $this->redirectToRoute('todo_list');

            }

        return $this->render('todo/edit.html.twig', array('todo' => $todo, 'form' => $form->createView()));
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);
        // replace this example code with whatever you need
        return $this->render('todo/details.html.twig', array('todo' => $todo));
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')->find($id);
        $em->remove($todo);
        $em->flush();

        $this->addFlash('notice',
            'Todo deleted'
        );
        return $this->redirectToRoute('todo_list');
    }
}
