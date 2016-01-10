<?php
	//验证码类
	class Captcha{
		//attribute
		private $width=80;		//picture width
		private $height=25;	//picture heigh
		private $length=4;	//captcha length
		private $lines=5;		//number of lines
		private $pixels=100;		//number of pixels
		private $bg_color_min=0;	//color config
		private $bg_color_max=100;	//color config
		private $font_color_min=150;	//color config
		private $font_color_max=255;	//color config
		private $line_color_min=150;	//color config
		private $line_color_max=200;	//color config
		private $pixels_color_min=150;	//color config
		private $pixels_color_max=200;	//color config
		private $font=5; 		//size of font
		private $string='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';		//string of captcha
		/**
		 * __construct()
		 * @param array $arr a array include attributes
		 */
		public function __construct($arr=array()){
			//初始化属性
			$this->width=isset($arr['width'])?$arr['width']:80;
			$this->height=isset($arr['height'])?$arr['height']:25;
			
/* 			$this->width=isset($arr['width'])?$arr['width']:200;
			$this->height=isset($arr['height'])?$arr['height']:50;
			$this->length=isset($arr['length'])?$arr['length']:4;
			$this->lines=isset($arr['lines'])?$arr['lines']:5;
			$this->pixels=isset($arr['pixels'])?$arr['pixels']:200;
			$this->font=isset($arr['font'])?$arr['font']:5;
			//$this->string=isset($arr['string'])?$arr['string']: */
		}
		
		public function generateCaptcha(){
			//make image
			$im=imagecreatetruecolor($this->width, $this->height);
			
			//set background
			$bg_color =imagecolorallocate($im, 
					mt_rand($this->bg_color_min,$this->bg_color_max), 
					mt_rand($this->bg_color_min,$this->bg_color_max), 
					mt_rand($this->bg_color_min,$this->bg_color_max));
		
			//padding
			imagefill($im, 0, 0, $bg_color);
			
		
			//add lines
			for($i=0;$i<$this->lines;$i++){
				//set color
				$line_color=imagecolorallocate($im, 
						mt_rand($this->line_color_min,$this->line_color_max), 
						mt_rand($this->line_color_min,$this->line_color_max), 
						mt_rand($this->line_color_min,$this->line_color_max));
			
				//draw line
				imageline($im, mt_rand(0,$this->width),mt_rand(0,$this->height), 
					mt_rand(0,$this->width),mt_rand(0,$this->height), $line_color);
			}
		
			//add pixels
			for($i=0;$i<$this->pixels;$i++){
				//set color
				$pixels_color=imagecolorallocate($im,
						mt_rand($this->pixels_color_min,$this->pixels_color_max),
						mt_rand($this->pixels_color_min,$this->pixels_color_max),
						mt_rand($this->pixels_color_min,$this->pixels_color_max));
					
				//draw pixels
				imagesetpixel($im, mt_rand(0,$this->width),mt_rand(0,$this->height),
				$pixels_color);
			}
			
			//get captcha string
			$captcha=$this->generateString();
			//set color of font
			$str_color=imagecolorallocate($im, 
					mt_rand($this->font_color_min,$this->font_color_max), 
					mt_rand($this->font_color_min,$this->font_color_max), 
					mt_rand($this->font_color_min,$this->font_color_max));
			
			//put string to picture
			imagestring($im, $this->font, ceil($this->width/2)-20, ceil($this->height/2)-10, $captcha, $str_color);
			
			//save or output
			imagepng($im);
			
			//destroy res
			imagedestroy($im);
		
		}
		
		/**
		 * get string of captcha
		 * @return string random-looking string
		 */
		private function generateString(){
			$captchaStr='';
			for($i=0;$i<$this->length;$i++){
				$captchaStr .=$this->string[mt_rand(0, strlen($this->string)-1)];
			}
			$_SESSION['captcha']=$captchaStr;
			return $captchaStr;
		}

		/**
		 * 验证用户提交的验证码
		 * @param string $captcha，用户提交的验证码
		 * @return bool，成功返回true，失败返回FALSE
		*/
		public static function checkCaptcha($captcha){
			//验证码不区分大小写
			return (strtolower($captcha) === strtolower($_SESSION['captcha']));
		}
		
	}
	
/* 	
	$captcha=new Captcha();
	header('content-type:image/png');
	$captcha->generateCaptcha();
	 */
	
	
	
	
	
	
	
	