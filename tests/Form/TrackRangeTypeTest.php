<?php declare(strict_types=1);

namespace Tests\Form;

use App\Entity\Track;
use App\Form\Type\TrackRangeType;
use Symfony\Component\Form\Test\TypeTestCase;

class TrackRangeTypeTest extends TypeTestCase
{
    public function testFormBuildsFromTrack(): void
    {
        $track = (new Track())
            ->setStartPoint(10)
            ->setEndPoint(90)
            ->setPoints(100);

        $form = $this->factory->create(TrackRangeType::class, $track);

        $this->assertSame(['startPoint', 'endPoint', 'points'], array_keys($form->all()));
        $this->assertSame('10', $form->createView()->children['startPoint']->vars['value']);
    }

    public function testSubmitTrimsTrack(): void
    {
        $track = (new Track())->setPoints(100);

        $form = $this->factory->create(TrackRangeType::class, $track);
        $form->submit(['startPoint' => '20', 'endPoint' => '80', 'points' => '100']);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame(20, $track->getStartPoint());
        $this->assertSame(80, $track->getEndPoint());
    }
}
