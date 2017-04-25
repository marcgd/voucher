<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Voucher;
use AppBundle\Lib\Voucher\VoucherGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VoucherController extends Controller
{

    public function indexAction()
    {
        return $this->render('jsgrid.html.twig');
    }

    public function generateAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Create new voucher entity object
        $voucher = new Voucher();
        // Persist to db
        $em->persist($voucher);
        // Set object data
        $voucher_gen = new VoucherGenerator();
        $voucher->setUser($this->getUser());
        $voucher->setCode($voucher_gen->generate());
        // Send sql to db
        $em->flush();

        // Serialize object in json
        $serializer = $this->get('jms_serializer');
        $voucher = $serializer->serialize($voucher, 'json');

        return new Response($voucher);
    }

    public function redeemAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        // Look for valid unredeemed codes in db
        $criteria = array('code' => $code, 'redeemed' => false);
        $voucher = $em->getRepository('AppBundle:Voucher')->findBy($criteria);
        
        // Didnt find any so abort!
        if (empty($voucher)) {
            throw new \Exception('Already used or empty code!');
        }

        // Found one so lets update the redeemed flag
        $voucher[0]->setRedeemed(true);
        $em->flush();

        // Serialize object in json
        $serializer = $this->get('jms_serializer');
        $voucher = $serializer->serialize($voucher, 'json');

        return new Response($voucher);
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Find all vouchers for the logged user
        $criteria = array('user' => $this->getUser()->getId());
        $vouchers = $em->getRepository('AppBundle:Voucher')->findBy($criteria);

        // Serialize object in json
        $serializer = $this->get('jms_serializer');
        $vouchers = $serializer->serialize($vouchers, 'json');

        return new Response($vouchers);
    }
}
