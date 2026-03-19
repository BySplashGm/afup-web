<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Repository\TicketSpecialPriceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ExtendSpecialPriceAction extends AbstractController
{
    public function __construct(
        private readonly TicketSpecialPriceRepository $ticketSpecialPriceRepository,
    ) {}

    public function __invoke(int $id, AdminEventSelection $eventSelection): Response
    {
        $specialPrice = $this->ticketSpecialPriceRepository->get($id);

        if ($specialPrice === null) {
            throw $this->createNotFoundException();
        }

        $newDateEnd = clone $specialPrice->getDateEnd();
        $newDateEnd->modify(sprintf('+%d days', SpecialPriceAction::EXTEND_DAYS));
        $specialPrice->setDateEnd($newDateEnd);

        $this->ticketSpecialPriceRepository->save($specialPrice);

        $this->addFlash('notice', sprintf('La validité du token a été prolongée de %d jours.', SpecialPriceAction::EXTEND_DAYS));

        return $this->redirectToRoute('admin_event_special_price', [
            'id' => $eventSelection->event->getId(),
        ]);
    }
}
