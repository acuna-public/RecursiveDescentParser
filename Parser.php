<?php
  
  class Parser {
    
    public $value;
    
    private $content;
    private $column = 0;
    private $char = '';
    private $int_value = 0;
    private $token = 0;
    
    const semicolon = 0;
    const period = 1;
    const plus = 2;
    const minus = 3;
    const times = 4;
    const divide = 5;
    const assign = 6;
    const lparen = 7;
    const rparen = 8;
    const letter = 9;
    const number = 10;
    
    public function __construct ($content) {
      
      $this->content = $content;
      
      $this->nextsym ();
      $this->statement ();
      
      $this->value = $this->expression ();
      $this->statement ();  // flush ';'
      
    }
    
    private function factor () { // factor = number | '(' expression ')'
      
      $value = 0;
      
      switch ($this->token) {
        
        case self::number:
          
          $value = $this->int_value;
          $this->statement (); // flush number
        
        break;
        
        case self::lparen:
          
          $this->statement ();
          $value = $this->expression ();
          
          if ($this->token != self::rparen)
            $this->error ('Missing ")"');
          
          $this->statement (); // flush ')'
        
        break;
        
        default:
          $this->error ('Invalid token. Expecting number or (');
        break;
        
      }
      
      return $value;
      
    }
    
    private function term () { // term = factor { ( '*' | '/' ) factor }
      
      $left = $this->factor ();
      
      while ($this->token == self::times || $this->token == self::divide) {
        
        $saveToken = $this->token;
        $this->statement ();
        
        switch ($saveToken) {
          
          case self::times:
            $left *= $this->factor ();
          break;
          
          case self::divide:
            $left /= $this->factor ();
          break;
          
        }
        
      }
      
      return $left;
      
    }
    
    private function expression () { // expression = term { ( '+' | '-' ) term }
      
      $left = $this->term ();
      
      while ($this->token == self::plus || $this->token == self::minus) {
        
        $saveToken = $this->token;
        $this->statement ();
        
        switch ($saveToken) {
          
          case self::plus:
            $left += $this->term ();
          break;
          
          case self::minus:
            $left -= $this->term ();
          break;
          
        }
        
      }
      
      return $left;
      
    }
    
    private function statement () {
      
      while ($this->char == ' ')
        $this->nextsym ();
      
      if (is_numeric ($this->char)) {
        
        $this->int_value = 0;
        
        while (is_numeric ($this->char)) {
          
          $this->int_value = ($this->int_value * 10 + intval ($this->char, 10));
          $this->nextsym ();
          
        }
        
        $this->token = self::number;
        
      } else {
        
        switch ($this->char) {
          
          case ';':
            $this->nextsym ();
            $this->token = self::semicolon;
          break;
          
          case '.':
            $this->nextsym ();
            $this->token = self::period;
          break;
          
          case '+':
            $this->nextsym ();
            $this->token = self::plus;
          break;
          
          case '-':
            $this->nextsym ();
            $this->token = self::minus;
          break;
          
          case '*':
            $this->nextsym ();
            $this->token = self::times;
          break;
          
          case '/':
            $this->nextsym ();
            $this->token = self::divide;
          break;
          
          case '=':
            $this->nextsym ();
            $this->token = self::assign;
          break;
          
          case '(':
            $this->nextsym ();
            $this->token = self::lparen;
          break;
          
          case ')':
            $this->nextsym ();
            $this->token = self::rparen;
          break;
          
          default:
            if ($this->char) $this->error ('Illegal character "'.$this->char.'"');
          break;
          
        }
        
      }
      
      return $this->token;
      
    }
    
    private function error ($msg) {
      die ($msg);
    }
    
    private function nextsym () {
      
      $this->char = $this->content[$this->column];
      $this->column++;
      
    }
    
  }
