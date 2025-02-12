<?php

namespace tests;

use App\Entity\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase{
    /**
     * @dataProvider providePersonData
     */
    public function testPersonInitialization(string $name, string $walletCurrency): void
    {
        $person = new Person($name, $walletCurrency);
        $this->assertSame($name, $person->getName());
        $this->assertSame($walletCurrency, $person->getWallet()->getCurrency());
    }

    public function testHasFund(): void
    {
        $person = new Person('John', 'USD');
        $this->assertFalse($person->hasFund());

        $person->getWallet()->addFund(100.0);
        $this->assertTrue($person->hasFund());
    }

    public function testTransfertFund(): void
    {
        $person1 = new Person('John', 'USD');
        $person2 = new Person('Jane', 'USD');

        $person1->getWallet()->addFund(100.0);

        $person1->transfertFund(50.0, $person2);

        $this->assertSame(50.0, $person1->getWallet()->getBalance());
        $this->assertSame(50.0, $person2->getWallet()->getBalance());
    }

    public function testTransfertCurrencies(): void
    {
        $this->expectException(\Exception::class);

        $person1 = new Person('John', 'USD');
        $person2 = new Person('Jane', 'EUR');

        $person1->getWallet()->addFund(100.0);
        $person1->transfertFund(50.0, $person2);
    }

    public function testDivideWallet(): void
    {
        $person1 = new Person('John', 'USD');
        $person2 = new Person('Jane', 'USD');
        $person3 = new Person('Doe', 'EUR');

        $person1->getWallet()->addFund(100.0);

        $person1->divideWallet([$person2, $person3]);

        $this->assertSame(50.0, $person2->getWallet()->getBalance());
        $this->assertSame(0.0, $person3->getWallet()->getBalance());
        $this->assertSame(0.0, $person1->getWallet()->getBalance());
    }

    public static function providePersonData(): array
    {
        return [
            ['Alice', 'USD'],
            ['Bob', 'EUR'],
        ];
    }
}