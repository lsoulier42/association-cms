<?php

namespace App\Page\Type;

use App\Entity\SpecialPage;
use App\Page\AbstractPageType;
use App\Repository\AppointmentRepository;

class AppointmentsPageType extends AbstractPageType
{
    public function __construct(
        private readonly AppointmentRepository $appointmentRepository,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'appointments';
    }

    public function getLabel(): string
    {
        return 'Rendez-vous / agenda';
    }

    public function getTemplate(): string
    {
        return 'special_page/type/appointments.html.twig';
    }

    public function getData(SpecialPage $page): array
    {
        return [
            'appointments' => $this->appointmentRepository->findBy([], ['date' => 'DESC']),
        ];
    }
}
