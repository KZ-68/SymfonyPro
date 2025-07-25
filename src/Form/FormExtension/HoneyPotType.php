<?php

namespace App\Form\FormExtension;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\AbstractType;
use App\EventSubscriber\HoneyPotSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HoneyPotType extends AbstractType
{
    private LoggerInterface $logger;
    
    private RequestStack $requestStack;

    protected const HONEY_POT_FIELD_PHONE = "phone";

    protected const HONEY_POT_FIELD_RAISON = "raison";

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::HONEY_POT_FIELD_PHONE, TextType::class, $this->setHoneyPotConfiguration())
            ->add(self::HONEY_POT_FIELD_RAISON, TextType::class, $this->setHoneyPotConfiguration())
            ->addEventSubscriber(new HoneyPotSubscriber($this->logger, $this->requestStack))
        ;
    }

    public function setHoneyPotConfiguration(): array
    {
        return [
            'attr' => [
                'autocomplete' => 'off',
                'tabindex' => '-1',
                'hidden' => true
            ],
            'data' => '',
            'mapped' => false,
            'required' => false
        ];
    }
}