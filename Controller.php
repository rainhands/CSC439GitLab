<?php

/* This is the comment to test GitHub >w< */

declare(strict_types=1);

class YahtzeeController {
    
    private Yahtzee $model;
    private YahtzeeView $view;
    
    public function __construct(Yahtzee $model, YahtzeeView $view){
        $this->model = $model;
        $this->view = $view;
    }
    
    public function get_model() : Yahtzee { return $this->model; }
    
    public function get_view() : YahtzeeView { return $this->view; }
    
    public function get_possible_categories() : array {
        $rv = array();
        $rv = $this->model->get_scorecard();
        foreach($rv as $key=>$value){
          if(is_null($rv[$key]) == false){
            unset($rv[$key]);
          }
        }
        return $rv;
    }
    
    public function process_score_input(string $line) : int {
        if ($line === "exit" || $line === "q") {
            return -1;
        }
        
        try {
            $result = $this->model->calculate($line, $this->model->get_kept_dice());
            $this->model->update_scorecard($line, $result);
            } catch (YahtzeeException $e) {
            return -2;
        }
        return 0;
    }
    
    public function process_keep_input(string $line) : int {
        /*
        process_keep_input(string $line) : int
        Processes the input from the user when determining which dice to keep.
        > If the line given is "exit" or "q", return -1.
        > If the line given is "all", immediately combine dice and return 0;
        > If the line given is "none" "pass" or blank "", return -2.
        - Otherwise, indexes are given
          > Attempt to call keep_by_index with the given line, return the int
            it returns, indicating remaining dice.
          > If keep_by_index throws an exception, catch it and return -2.
            (output the error message with the view).
        */
        
        if($line == "exit" || $line == "q"){
            return -1;
        }
        if($line == "" || $line == "none" || $line == "pass"){
            return -2;
        }
        if($line == "all"){
           $this->model->combine_dice();
           return 0;
        }
        try{
          $result =  $this->model->keep_by_index($line);
        }
        catch(Exception $result){
          $this->view->output(strval($result));
          return -2;
         }
          return $result;
      
       
    }
    
    public function handle_roll() : int {
        /* 
        handle_roll() : int
        This method rolls the dice, up to three times, and gathers input from
        the user on what to do with each roll.  This method should:
        - Enter a loop that runs twice, and set a variable to track
          $remaining_dice to 5.
        - Each loop:
          - Roll dice, the amount being whats in $remaining_dice. 
          - Take the user input, feed it into process_keep_input()
            > If the return value is -2, nothing happens, continue the loop.
            > If the return value is -1, return -1.
            > else, set the $remaining_dice local var to the return value.
          > If the $remaining_dice is 0, break out of the loop.
        > After the loop, if the model's kept_dice has less than 5 dice,
          roll a final time, and then combine dice the kept_dice should be 
          correct based on the rolls and inputs.
        > Return 0.
          
        Hint for this one: What you can test from the result of this method is
        that 1) the kept_dice is properly populated given a variety of
        scenarios (simulate different inputs) and 2) the return value is
        correct.  You'll want to stub the view to return pre-scripted
        responses, a good way to do this is a stub's onConsecutiveCalls method.
        */
      $remaining_dice = 5;
     
      for($i = 0; $i < 2; $i++){
       
        $this->model->roll($remaining_dice);
        $inputValue = $this->get_view()->get_user_input("test");
      //  echo(strval($remaining_dice));
        $result = $this->process_keep_input($inputValue);
        if($result == -2){}
        elseif($result == -1){
          return -1;
        }
        else{
          $remaining_dice = $result;
        }
        if($remaining_dice == 0){
          break;
        }
      }
      $diceNum = array_sum($this->model->get_kept_dice());
      if($diceNum < 5){
        $this->model->roll($diceNum);
        $this->model->combine_dice();
      }

        
        return 0;
    }
    
    public function main_loop() : int {
        /*
        main_loop() : int
        Runs the main loop of the controller.
        > The loop should run exactly 13 times, and the model's current_turn
        should be incremented each time.
        - (Output the current turn with the view)
        > If handle_roll returns -1, return -1.
        - (Output the kept dice after handle_roll with the view)
        - Get the valid categories calling get_possible_categories
        - (Output valid categories with view)
        - (Get user input with view)
        > Call process_score_input on the result from the user input.
          > If the returning values is -1, return -1.
        - (use the view to output the score)
        > At the end of each loop iteration, clear_kept_dice and increment_turn
        
        Hint: For this, you can inspect the model to see if behavior is being
        properly called/implemented.  You will also need to stub view to give
        pre-scripted inputs.  Remember, we primarily want to test data and
        behavior, NOT implementation details.
        */

        for($i = 0; $i < 13; $i++){
          $this->view->output_turn();
          $rollValue = $this->handle_roll();
          $this->view->output_kept_dice();
          if($rollValue == -1){
            return -1;
          }
          $availableCategories = $this->get_possible_categories();
          $this->view->output_array($availableCategories);
          $userInput = $this->get_view()->get_user_input("test");
          $keptInputValue = $this->process_keep_input("$userInput");
          if($keptInputValue == -1){
            return -1;
          }
          $this->view->output_score();
          $this->model->clear_kept_dice();
          $this->model->increment_turn();
        }
        
        
        return 0;
        
    }
}


?>

 
