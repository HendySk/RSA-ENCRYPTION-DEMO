<?php

class rsa{
    function __construct($pr){
        $this->p=0;$this->q=0; $this->f=65537;$this->m=0; $this->g=0; 
        $this->k=0; $this->d=0; $this->n=0;$this->t=0;
		$this->pr=(($pr%128)!=0)? $pr -($pr%128)+128:$pr;
		
    }

    function test_co_primes($a, $b)
    {
        if (gmp_gcd($a,$b) == 1)return true;
        return false;
    }
    function getRand($precision) {
        gmp_random_seed(time());
        return gmp_random_bits($precision);
    }
    function nextPrime($p) {
        $q=gmp_add($p,1);
        while (gmp_prob_prime($q, 10)==0) {
            $q=gmp_add($q,1);
        }
        return $q;
    }
    function getPrimeRand($precision) {
        gmp_random_seed(time());
        $r=gmp_random_range (str_repeat("1", $precision) ,str_repeat("9", $precision));
        $a="1";
        $b="2";
        $loop=1;
        while (gmp_prob_prime($r, 10)==0) {
			if($loop<8){$a= strval($loop);$b= strval($loop+1);}
            if($loop==8){$loop=1;gmp_random_seed(time());}
			$r=gmp_random_range (str_repeat($a, $precision) , str_repeat($b, $precision));
		}
        return $r;
    }
    function initialise(){
        $this->q = $this->nextPrime($this->q);
        $this->m = ($this->p - 1)*($this->q - 1);
        $this->n = $this->p*$this->q;
    }
    function generate(){
        $T=microtime(true);
        $this->p = $this->getPrimeRand(ceil(($this->pr* log(2,10))/2));
        $this->q = $this->nextPrime($this->p);
        $this->initialise();
		$cpt=0;
        while (($this->g != 1)|($this->d>$this->m)|($this->d<0)) {
            if($cpt==1){
                $this->initialise();
            }
			if($cpt>=2)$cpt=0;
            $cpt++;
            $gcdext=gmp_gcdext($this->f, $this->m);
            $this->g=$gcdext['g']; $this->d= $gcdext['s']; $this->k= $gcdext['t'];
        }
        $TT=microtime(true)-$T;
		$this->t=$TT;
		$this->n=base64_encode($this->n);$this->f=base64_encode($this->f);$this->d=base64_encode($this->d);
        //$keys=array("n"=>$this->n,"f"=>$this->f,"d"=>$this->d,'t'=>$TT);
        
    }
    
	function to_hex($txt){
		$output="";
		while ($txt){
			$part = substr($txt, 0,1);
            $txt = substr($txt, 1);
			$hexchar= dechex(ord($part));
			if(strlen($hexchar)==1)$hexchar="0".$hexchar;
			$output .= $hexchar;
		}
		return $output;
	}
	function toText($txt){
		$output="";
		while ($txt){
			$part = substr($txt, 0,2);
            $txt = substr($txt, 2);
			$output .= chr(hexdec($part));
		}
		return $output;
	}

	function enc($txt,$pw) {
		$txt= base64_encode(gzencode ($txt,9));
		$txt=$this->to_hex($txt);
		$segment = (ceil($this->pr / 8));
		$output = '';
		while ($txt){
			$part = substr($txt, 0, $segment);
			$txt = substr($txt, $segment);
			$tmpStr= gmp_strval(gmp_powm(gmp_init('0x'.$part),base64_decode($this->f),base64_decode($this->n)),16);
			$diff= (($segment*2)+1)-strlen($tmpStr);
			$tmpStr=str_repeat("0",$diff).$tmpStr;
			$output .=$tmpStr;
		}
		gmp_random_seed(time());
		$r=gmp_random_range(1,strlen($output));
		//signed message
		$output=substr($output,0,(int)$r).$this->pwMd5($pw).substr($output,(int)$r);
		return base64_encode($this->pwMd5($pw).$output);
    }
    
	function dec($txt,$pw)
    {	
        $output = '';
		$segment = (ceil($this->pr / 8)*2)+1;
		$txt=base64_decode($txt);
		$txt=implode('',explode($this->pwMd5($pw),$txt));
		while ($txt){
			$part = substr($txt, 0, $segment);
			$txt = substr($txt, $segment);
			$tmpstr=gmp_strval(gmp_powm (gmp_init('0x'.$part),base64_decode($this->d),base64_decode($this->n)),16);
			$output .=$this->toText($tmpstr);//$this->toText(
		}
		$output=base64_decode($output);
		error_reporting(E_ALL ^ E_WARNING); 
		return ($output=gzdecode ($output))?$output:"authentication failed";
    }
	function pwMd5($pw) { 
		return  hash("md5", $pw, false);
    } 
}

if(isset($_GET['pr'])){
    set_time_limit ( 100 );
    $disp="";
    $pr= $_GET['pr'];
    $rsa= new rsa($pr);
	$rsa->generate();
    $txt="Lorem ipsum dolor Curabiturat dolor sollicitudin, tempus libero nec, tempor mauris. Nam finibus tempus arcu, in laoreet nisl tincidunt ac. Nam velit sapien, sagittis sit amet arcu vehicula, pulvinar vehicula justo. Mauris facilisis lacus quam, eget hendrerit lectus imperdiet vel. Morbi vulputate eros quam, eget scelerisque urna gravida eget. Curabitur imperdiet odio sit amet sagittis ornare. Donec semper quam vel posuere scelerisque. Fusce in efficitur justo. Aliquam blandit, augue scelerisque eleifend ornare, libero arcu interdum metus, vitae bibendum elit felis quis quam. Nulla sed elementum diam, non dictum massa. Donec dictum eleifend mi ac laoreet.";
    $disp ="Total execution time= : ".$rsa->t." seconds".'<br><br>';
    //$disp .="______Public Exponent______:".'<br>'.$rsa->f.'<br><br>';
    $disp.="______Modulus (also refered to as public key)______:".'<br>'.$rsa->n.'<br><br>';
    $disp.="______Private Exponent (also refered to as private key)______:".'<br>'.$rsa->d.'<br><br>';
    $disp.= "______Number of digits (decimals for private and public keys)______:".'<br>'.strlen(base64_decode($rsa->n)).'<br><br>';
	$txt2=$rsa->enc($txt,"some password or signature here");
	$disp.= "______Plain texte______:".'<br>'.$txt.'<br><br>';
    $disp.= "______Encrypted______:".'<br>'.$txt2.'<br><br>';
	$txt3=$rsa->dec($txt2,"some password or signature her");
    $disp.= "______Decrypted______:".'<br>'.$txt3;
    echo $disp;
    
}
?>