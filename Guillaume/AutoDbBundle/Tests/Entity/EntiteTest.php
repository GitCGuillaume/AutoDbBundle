<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\EntiteA;
use AppBundle\Entity\EntiteB;
use AppBundle\Entity\EntiteC;
use AppBundle\Entity\EntiteD;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class EntiteTest extends KernelTestCase{

	public function testSetEntite(){
		//EntiteD
		$entiteD = new EntiteD();
		$entiteD->setId(1);
		$entiteD->setstringNull("");
		$entiteD->setstringNotNull("ffff");
		$result = $entiteD;
		foreach ($result as $key => $value) {
			$this->assertEquals(1, $value);
			$this->assertEquals("", $value);
			$this->assertEquals("ffff", $value);
		}

		//EntiteC
		$entiteC = new EntiteC();
		$entiteC->setId(1);
		$entiteC->setboolC(1);
		$entiteC->setboolCNull("");
		$entiteC->setboolTrueNull(1);
		$entiteC->setboolTrue(1);
		$entiteC->setEntiteD($entiteD);
		$result2 = $entiteC;
		foreach ($result2 as $key => $value) {
			$this->assertEquals(1, $value);
			$this->assertEquals(true, $value);
			$this->assertEquals("", $value);
			$this->assertEquals(1, $value);
			$this->assertEquals(1, $value);
			$this->assertEquals($entiteD, $value);
		}

		//EntiteB
		$entiteB = new EntiteB();
		$entiteB->setId(1);
		$entiteB->settextNull("");
		$entiteB->settextNotNull("test text not null");
		$entiteB->setboolEntiteB(1);
		$entiteB->addEntiteC($entiteC);
		$entiteB->setEntiteD($entiteD);
		$result = $entiteB;
		foreach ($result as $key => $value) {
			$this->assertEquals(1, $value);
			$this->assertEquals("", $value);
			$this->assertEquals("test text not null", $value);
			$this->assertEquals(1, $value);
			$this->assertEquals($entiteC, $value);
		}

		//EntiteA
		$entiteA = new EntiteA();
		$entiteA->setId(1);
		$entiteA->setboolTrue(true);
		$entiteA->setnomANull("nomIsNull");
		$entiteA->setnomA("nomB");
		$entiteA->setEntiteB($entiteB);
		$result = $entiteA;
		foreach ($result as $key => $value) {
			$this->assertEquals(1, $value);
			$this->assertEquals(true, $value);
			$this->assertEquals("nomIsNull", $value);
			$this->assertEquals("nomB", $value);
			$this->assertEquals($entiteB, $value);
		}
	}
}
