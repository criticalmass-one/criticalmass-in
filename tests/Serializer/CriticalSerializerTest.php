<?php declare(strict_types=1);

namespace Tests\Serializer;

use App\Serializer\CriticalSerializer;
use App\Serializer\CriticalSerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CriticalSerializerTest extends TestCase
{
    private CriticalSerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = new CriticalSerializer();
    }

    public function testSerializeSimpleObject(): void
    {
        $object = new TestModel();
        $object->setId(42);
        $object->setName('Test Name');
        $object->setDescription('Test Description');

        $json = $this->serializer->serialize($object);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertEquals(42, $data['id']);
        $this->assertEquals('Test Name', $data['name']);
        $this->assertEquals('Test Description', $data['description']);
    }

    public function testDeserializeSimpleObject(): void
    {
        $json = '{"id": 42, "name": "Test Name", "description": "Test Description"}';

        $object = $this->serializer->deserialize($json, TestModel::class);

        $this->assertInstanceOf(TestModel::class, $object);
        $this->assertEquals(42, $object->getId());
        $this->assertEquals('Test Name', $object->getName());
        $this->assertEquals('Test Description', $object->getDescription());
    }

    public function testCamelCaseToSnakeCaseConversion(): void
    {
        $object = new TestModelWithCamelCase();
        $object->setFirstName('John');
        $object->setLastName('Doe');
        $object->setEmailAddress('john@example.com');

        $json = $this->serializer->serialize($object);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('first_name', $data);
        $this->assertArrayHasKey('last_name', $data);
        $this->assertArrayHasKey('email_address', $data);
        $this->assertEquals('John', $data['first_name']);
        $this->assertEquals('Doe', $data['last_name']);
        $this->assertEquals('john@example.com', $data['email_address']);
    }

    public function testSnakeCaseToCamelCaseDeserialization(): void
    {
        $json = '{"first_name": "Jane", "last_name": "Smith", "email_address": "jane@example.com"}';

        $object = $this->serializer->deserialize($json, TestModelWithCamelCase::class);

        $this->assertInstanceOf(TestModelWithCamelCase::class, $object);
        $this->assertEquals('Jane', $object->getFirstName());
        $this->assertEquals('Smith', $object->getLastName());
        $this->assertEquals('jane@example.com', $object->getEmailAddress());
    }

    public function testDateTimeSerialization(): void
    {
        $object = new TestModelWithDateTime();
        $object->setCreatedAt(new \DateTime('2024-06-15 14:30:00', new \DateTimeZone('Europe/Berlin')));

        $json = $this->serializer->serialize($object);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('created_at', $data);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/', $data['created_at']);
    }

    public function testDateTimeDeserialization(): void
    {
        $json = '{"created_at": "2024-06-15T14:30:00+02:00"}';

        $object = $this->serializer->deserialize($json, TestModelWithDateTime::class);

        $this->assertInstanceOf(TestModelWithDateTime::class, $object);
        $this->assertInstanceOf(\DateTimeInterface::class, $object->getCreatedAt());
        $this->assertEquals('2024-06-15', $object->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals('14:30:00', $object->getCreatedAt()->format('H:i:s'));
    }

    public function testSkipNullValues(): void
    {
        $object = new TestModel();
        $object->setId(42);
        $object->setName('Test Name');
        // description is not set, should be null

        $json = $this->serializer->serialize($object);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayNotHasKey('description', $data);
    }

    public function testCircularReferenceHandling(): void
    {
        $parent = new TestModelWithRelation();
        $parent->setId(1);
        $parent->setName('Parent');

        $child = new TestModelWithRelation();
        $child->setId(2);
        $child->setName('Child');

        $parent->setRelation($child);
        $child->setRelation($parent);

        $json = $this->serializer->serialize($parent);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(1, $data['id']);
        $this->assertArrayHasKey('relation', $data);
        $this->assertArrayHasKey('id', $data['relation']);
        $this->assertEquals(2, $data['relation']['id']);
        // The circular reference to parent should be resolved to just the ID
        $this->assertEquals(1, $data['relation']['relation']);
    }

    public function testSerializeArray(): void
    {
        $objects = [
            (new TestModel())->setId(1)->setName('First'),
            (new TestModel())->setId(2)->setName('Second'),
            (new TestModel())->setId(3)->setName('Third'),
        ];

        $json = $this->serializer->serialize($objects);
        $data = json_decode($json, true);

        $this->assertCount(3, $data);
        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals('First', $data[0]['name']);
        $this->assertEquals(2, $data[1]['id']);
        $this->assertEquals('Second', $data[1]['name']);
        $this->assertEquals(3, $data[2]['id']);
        $this->assertEquals('Third', $data[2]['name']);
    }

    public function testDeserializeArray(): void
    {
        $json = '[{"id": 1, "name": "First"}, {"id": 2, "name": "Second"}]';

        $objects = $this->serializer->deserialize($json, TestModel::class.'[]');

        $this->assertCount(2, $objects);
        $this->assertInstanceOf(TestModel::class, $objects[0]);
        $this->assertInstanceOf(TestModel::class, $objects[1]);
        $this->assertEquals(1, $objects[0]->getId());
        $this->assertEquals('First', $objects[0]->getName());
        $this->assertEquals(2, $objects[1]->getId());
        $this->assertEquals('Second', $objects[1]->getName());
    }

    public function testSerializeWithGroups(): void
    {
        $object = new TestModelWithGroups();
        $object->setId(42);
        $object->setPublicName('Public Name');
        $object->setPrivateData('Secret Data');

        $json = $this->serializer->serialize($object, 'json', [
            AbstractNormalizer::GROUPS => ['public'],
        ]);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('public_name', $data);
        $this->assertArrayNotHasKey('private_data', $data);
    }

    public function testDefaultJsonFormat(): void
    {
        $object = new TestModel();
        $object->setId(42);
        $object->setName('Test');

        // serialize without specifying format should use json
        $json = $this->serializer->serialize($object);

        $this->assertJson($json);
    }

    public function testContextOverride(): void
    {
        $object = new TestModel();
        $object->setId(42);
        $object->setName('Test');
        // description is null

        // Override the default context to include null values
        $json = $this->serializer->serialize($object, 'json', [
            \Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer::SKIP_NULL_VALUES => false,
        ]);
        $data = json_decode($json, true);

        $this->assertArrayHasKey('description', $data);
        $this->assertNull($data['description']);
    }
}

// Test model classes

class TestModel
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}

class TestModelWithCamelCase
{
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $emailAddress = null;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(?string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }
}

class TestModelWithDateTime
{
    private ?\DateTimeInterface $createdAt = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}

class TestModelWithRelation
{
    private ?int $id = null;
    private ?string $name = null;
    private ?TestModelWithRelation $relation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getRelation(): ?TestModelWithRelation
    {
        return $this->relation;
    }

    public function setRelation(?TestModelWithRelation $relation): self
    {
        $this->relation = $relation;
        return $this;
    }
}

class TestModelWithGroups
{
    #[Groups(['public', 'private'])]
    private ?int $id = null;

    #[Groups(['public'])]
    private ?string $publicName = null;

    #[Groups(['private'])]
    private ?string $privateData = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getPublicName(): ?string
    {
        return $this->publicName;
    }

    public function setPublicName(?string $publicName): self
    {
        $this->publicName = $publicName;
        return $this;
    }

    public function getPrivateData(): ?string
    {
        return $this->privateData;
    }

    public function setPrivateData(?string $privateData): self
    {
        $this->privateData = $privateData;
        return $this;
    }
}
