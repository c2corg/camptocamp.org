<?php
/***********************************************************************
  Title: CaptchaBox v1.0.4
  Author: Mathias Michel (mmichel@chez.com)
  Date: 2006-12-06
  Description: Class to handle captcha that replaces submit.


(c) 2004  Kai Blankenhorn (kaib@bitfolge.de)
(c) 2006  Mathias Michel 

Derived from a work of Kai Blankenhorn, botproof email v3.1

************************************************************************
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

************************************************************************
Changelog:

2006-12-06 : add a check if x and y variables exists, to avoid php notice
2006-08-30 : add a check if session exists (thanks to smartys)
2006-05-09 : initial release

***********************************************************************/

/**
 * A Class that generates a picture with a small box that has to be clicked..
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @contributor Mathias Michel <mmichel@chez.com> 
 */

	// Constants to define return value of the Check() function.
	define("CHECK_OK", 0);
	define("CHECK_KO", 1);
	define("CHECK_MAX_TRIES", 2);
	// I set this like the KO, but it should be 3. I did that because I think
	// we don't need to know the difference (less cases to check in program)
	// For debug, you can set it to 3.
	define("CHECK_NO_SESSION", 1); 

class CaptchaBox {
	
	/**
	 * how many attemps to grant the user to master the challenge
	 * @access public
	 */
	var $maxTries = 3;

	/**
	 * the background color of the generated image. Specified as a 3 element array for R, G and B values. Defaults to white.
	 * @access public
	 */
	var $background;

	/**
	 * the foreground color of the generated image. Specified as a 3 element array for R, G and B values. Defaults to dark blue.
	 * @access public
	 */
	var $foreground;

	/**
	 * the width of the generated image
	 * @access private
	 */
	var $imageX = 0;

	/**
	 * the height of the generated image
	 * @access private
	 */
	var $imageY = 0;

	/**
	 * the background color of the generated image after it has been allocated. Specified as an integer.
	 * @access private
	 */
	var $bgAllocated;

	/**
	 * the foreground color of the generated image after it has been allocated. Specified as an integer.
	 * @access private
	 */
	var $fgAllocated;

	// all variables are private
	var $boxPosX;
	var $boxPosy;
	var $boxX;
	var $boxY;
	
	/**
	 * Constructor of CaptchaBox. 
	 * Initializes foreground and background colors.
	 * Initializes X & Y coordinates for image and box.	 
	 * @access public
	 */
	function CaptchaBox() {
    $mobile_version = c2cTools::mobileVersion();
    $this->boxX = $mobile_version ? 40 : 20;
    $this->boxY = $mobile_version ? 40 : 20;
		$this->background = Array(255,255,255);
		$this->foreground = Array(0,0,128);
		$this->boxPosX = ($_SESSION["x"]?$_SESSION["x"]:null);
		$this->boxPosY = ($_SESSION["y"]?$_SESSION["y"]:null);
		$this->imageX = $mobile_version ? 292 : 200;
		$this->imageY = 150;
		
	}


	/**
	 * Prepare the image the challenge is drawn on.
	 * @access private
	 * @return image a GDLib image resource
	 */
	function _createImage() {
		Header("Content-type: image/png");
		if (function_exists("imagecreatetruecolor")) {
			$im = imagecreatetruecolor ($this->imageX, $this->imageY);
		} else {
			$im = imagecreate ($this->imageX, $this->imageY)
				or die ("Cannot Initialize new GD image stream");
		}
		if (function_exists("imageantialias")) {
			imageantialias($im, true);
		}
		$this->bgAllocated = imagecolorallocate($im, rand(224,256), rand(224,256), rand(224,256));
		$this->fgAllocated = imagecolorallocate($im, $this->foreground[0], $this->foreground[1], $this->foreground[2]);
		imagefill($im, 0, 0, $this->bgAllocated);
		return $im;		 
	}
	

	/**
	 * Generate some random lines on the image.
	 * @param image $im a GDLib image resource to modify
	 * @param int $amount (optional) the number of lines to draw
	 * @param char $direction the direction of the lines ('x'=horizontal, 'y'=vertical)
	 * @access private
	 */
	function _generateImageNoise($im,$amount=5,$direction="y") {
		$x = imagesx($im);
		$y = imagesy($im);
		for ($i=0;$i<$amount;$i++) {
			$color = imagecolorallocate($im, rand(64,192), rand(64,192), rand(64,192));
			if ($direction=="y") {
				$r1 = rand(0,$x);
				imageline($im, $r1, 0, $r1+rand(-5,5), $y, $color);
			} else {
				$r1 = rand(0,$y);
				imageline($im, 0, $r1, $x, $r1+rand(-5,5), $color);
			}
		}
	}
	
	
	function reset() {
		$_SESSION["x"] = null;
		$_SESSION["y"] = null;
		$this->boxPosX = null;
		$this->boxPosY = null;
	}
	
	function generateImage() {
		$_SESSION["x"] = rand(0,$this->imageX-$this->boxX);
		$_SESSION["y"] = rand(0,$this->imageY-$this->boxY);
		
		$text_color = 0;
		
		$im = $this->_createImage();
		imagefilledrectangle($im, $_SESSION["x"], $_SESSION["y"], $_SESSION["x"]+$this->boxX, $_SESSION["y"]+$this->boxY, imagecolorallocate($im, 128+rand(-32,32), 128+rand(-32,32), 128+rand(-32,32)));
		$this->_generateImageNoise($im, $this->imageX/6, "x");
		$this->_generateImageNoise($im, $this->imageY/6, "y");
		imagepng($im);
	}
	
	function _doCheck() {
		return
			$_POST["x"]>=$_SESSION["x"] AND 
			$_POST["x"]<=$_SESSION["x"]+$this->boxX AND
			$_POST["y"]>=$_SESSION["y"] AND 
			$_POST["y"]<=$_SESSION["y"]+$this->boxY;
	}

	function check() {
		if ((session_id()=="") or !isset($_SESSION['captchabox']) or
        !isset($_SESSION["x"]) or ! isset($_SESSION["y"])) {
			return CHECK_NO_SESSION; // no session found, probably a bot
		};
		if ($_SESSION["captchabox_tries"]>$this->maxTries) {
			return CHECK_MAX_TRIES; // max tries Reached
		} else {
			if ($_SESSION["captchabox_tries"]=="") {
				$_SESSION["captchabox_tries"] = 1;
			} else { 
				$_SESSION["captchabox_tries"]++;
			}
			if($this->_doCheck()) {
				$_SESSION["captchabox_tries"] = 0;
				return CHECK_OK; // all is ok
			} else {
				if ($_SESSION["tries"]>$this->maxTries) {
					return CHECK_MAX_TRIES; //maxTries Reached
				} else {
					return CHECK_KO; 
				}					
		  }
	  }
  }
} // class end

?>
