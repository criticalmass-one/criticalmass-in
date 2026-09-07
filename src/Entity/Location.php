<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Router\Attribute as Routing;
use App\Criticalmass\Router\Attribute\RouteParameter;
use App\EntityInterface\AuditableInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use Jsor\Doctrine\PostGIS\Types\PostGISType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[Routing\DefaultRoute(name: 'caldera_criticalmass_location_show')]
#[ORM\Table(name: 'location')]
#[ORM\Index(fields: ['coordinates'], name: 'location_coordinates_gist', flags: ['spatial'])]
#[ORM\Entity(repositoryClass: 'App\Repository\LocationRepository')]
class Location implements RouteableInterface, AuditableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['location'])]
    protected ?int $id = null;

    #[RouteParameter(name: 'citySlug')]
    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'locations')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?City $city = null;

    #[RouteParameter(name: 'slug')]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['location'])]
    protected ?string $slug = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['location'])]
    protected ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['location'])]
    protected ?float $longitude = null;
    /**
     * Dieselbe Stelle noch einmal, diesmal als Geometrie.
     *
     * Gefuehrt wird sie aus latitude und longitude, nicht umgekehrt: Die beiden
     * Spalten haengen an der API-Ausgabe und an den Formularen. Der Gewinn
     * liegt beim raeumlichen Index — eine Umkreissuche ueber ST_DWithin kann
     * ihn nutzen, die Haversine-Formel ueber zwei Fliesskommaspalten nicht.
     */
    #[ORM\Column(type: PostGISType::GEOMETRY, nullable: true, options: ['geometry_type' => 'POINT', 'srid' => 4326])]
    #[Ignore]
    protected ?string $coordinates = null;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['location'])]
    protected ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['location'])]
    protected ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLatitude(?float $latitude = null): Location
    {
        $this->latitude = $latitude;
        $this->punktNachfuehren();

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(?float $longitude = null): Location
    {
        $this->longitude = $longitude;
        $this->punktNachfuehren();

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    /**
     * Haelt die Geometrie an den beiden Fliesskommaspalten nach.
     *
     * In WKT steht die Laenge vor der Breite — anders herum als in jeder
     * Beschriftung dieser Anwendung, und eine beliebte Fehlerquelle.
     *
     * Null und exakt 0 gelten als "keine Angabe": Der Punkt 0,0 liegt im Golf
     * von Guinea, und der Bestand nutzt ihn seit jeher als Platzhalter — bei
     * den Fotos betrifft das 18 Zeilen.
     */
    private function punktNachfuehren(): void
    {
        if (null === $this->latitude || null === $this->longitude
            || 0.0 === $this->latitude || 0.0 === $this->longitude) {
            $this->coordinates = null;

            return;
        }

        $this->coordinates = sprintf('SRID=4326;POINT(%.8F %.8F)', $this->longitude, $this->latitude);
    }

    public function setDescription(?string $description = null): Location
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setCity(?City $city = null): Location
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setTitle(?string $title = null): Location
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSlug(?string $slug = null): Location
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function hasCoordinates(): bool
    {
        return ($this->latitude && $this->longitude);
    }
}
