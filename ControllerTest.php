<?php
require_once 'Main.php';

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    private $model;
    private $view;
    private $sut;
    
    public function setUp() : void {
        $d = new YahtzeeDice();
        $this->model = new Yahtzee($d);
        $this->view = $this->createStub(YahtzeeView::class);
        $this->sut = new YahtzeeController($this->model, $this->view);
    }
    
    public function test_get_model(){
        $result = $this->sut->get_model();
        $this->assertNotNull($result);
    }
    
    public function test_get_view(){
        $result = $this->sut->get_view();
        $this->assertNotNull($result);
    }
    
    public function test_get_possible_categories(){
        $cats = $this->sut->get_possible_categories();
        $this->assertNotEmpty($cats);
    }
    
    public function test_process_score_input() {
        $f = new YahtzeeDice();
        $this->sut = new Yahtzee($f);
        $view = new YahtzeeView($this->sut);
        $this->d = new YahtzeeController($this->sut, $view);
        
        $this->assertEquals($this->d->process_score_input("invalid_category"), -2);
        
        $this->sut->update_scorecard("ones", 3); // Set a valid score for testing
        $this->assertEquals($this->d->process_score_input("ones"), 0); 
    }

}