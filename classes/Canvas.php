<?php
    namespace GSMap;
    class Canvas {
        private $canvas;
        private $type = 'png';
        public function __construct($w=1, $h=1, array $startColor=null) {
            if (is_numeric($w) === false && is_string($w) === true) {
                $this->canvas = $this->getResource($w, true);
            } else {
                $this->canvas = imagecreatetruecolor($w, $h);
                if ($startColor === null && count($startColor) !== 3) {
                    $startColor = array(255,255,255);
                }
                $color = imagecolorallocate($this->canvas, $startColor[0], $startColor[1], $startColor[2]);
                imagefill($this->canvas, 0, 0, $color);
            }
            return true;
        }
        public function addToSide($image, $adjustX=0) {
            $image = $this->getResource($image);

            $addCanvasSize = $this->getCanvasSize($image);
            $currentCanvasSize = $this->getCanvasSize();

            $addw = $addCanvasSize['width'];
            $addh = $addCanvasSize['height'] - $currentCanvasSize['height'];
            if ($addh < 0) {
                $addh = 0;
            }
            $this->resizeCanvas($addw, $addh);

            imagealphablending($this->canvas, true);
            imagesavealpha($this->canvas, true);
            imagecopy($this->canvas, $image, $currentCanvasSize['width']+$adjustX, 0, 0, 0, $addCanvasSize['width'], $addCanvasSize['height']);
        }
        public function addToBottom($image) {
            $image = $this->getResource($image);

            $addCanvasSize = $this->getCanvasSize($image);
            $currentCanvasSize = $this->getCanvasSize();

            $addh = $addCanvasSize['height'];
            $addw = $addCanvasSize['width'] - $currentCanvasSize['width'];
            if ($addw < 0) {
                $addw = 0;
            }
            $this->resizeCanvas($addw, $addh);

            imagealphablending($this->canvas, true);
            imagesavealpha($this->canvas, true);
            imagecopy($this->canvas, $image, 0, $currentCanvasSize['height'], 0, 0, $addCanvasSize['width'], $addCanvasSize['height']);
        }
        public function addToTop($image) {
            $image = $this->getResource($image);

            $addCanvasSize = $this->getCanvasSize($image);
            $currentCanvasSize = $this->getCanvasSize();

            $addh = $addCanvasSize['height'];
            $addw = $addCanvasSize['width'] - $currentCanvasSize['width'];
            if ($addw < 0) {
                $addw = 0;
            }
            $this->resizeCanvas($addw, $addh, 'top');

            imagealphablending($this->canvas, true);
            imagesavealpha($this->canvas, true);
            imagecopy($this->canvas, $image, 0, 0, 0, 0, $addCanvasSize['width'], $addCanvasSize['height']);
        }
        public function addXY($image, $x, $y) {
            $image = $this->getResource($image);
            $addCanvasSize = $this->getCanvasSize($image);
            imagealphablending($this->canvas, true);
            imagesavealpha($this->canvas, true);
            imagecopy($this->canvas, $image, $x, $y, 0, 0, $addCanvasSize['width'], $addCanvasSize['height']);
        }


        private function getResource($image, $setInternalType=false) {
            if ($image instanceof \GSMap\Canvas) {
                return $image->getCanvas();
            }
            if (gettype($image) == 'resource') {
                return $image;
            }
            if (is_string($image) === true) {
                $info = getimagesize($image);
                $mime = $info['mime'];

                switch ($mime) {
                    case 'image/jpeg':
                        $image_create_func = 'imagecreatefromjpeg';
                        $image_save_func = 'imagejpeg';
                        $type = 'jpg';
                        break;

                    case 'image/png':
                        $image_create_func = 'imagecreatefrompng';
                        $image_save_func = 'imagepng';
                        $type = 'png';
                        break;

                    case 'image/gif':
                        $image_create_func = 'imagecreatefromgif';
                        $image_save_func = 'imagegif';
                        $type = 'gif';
                        break;

                    default:
                        throw new \Exception('Unknown image type: '.$image);
                }
                $src = $image_create_func($image);

                if ($setInternalType === true) {
                    $this->type = $type;
                }
                return $src;
            }

            throw new \Exception('Cannot create image resource, unknown type');
        }
        public function getCanvasSize($canvas=false) {
            if ($canvas === false)
                $canvas = $this->canvas;
            return array(
                'width'=>imagesx($canvas),
                'height'=>imagesy($canvas)
            );
        }
        private function resizeCanvas($addToWidth=0, $addToHeight=0, $whereHeight='bottom', $whereWidth='right') {
            $oldw = imagesx($this->canvas);
            $oldh = imagesy($this->canvas);
            $newimage = imagecreatetruecolor($oldw+$addToWidth, $oldh+$addToHeight); // Creates a black image
            $white = imagecolorallocate($newimage, 255, 255, 255);
            imagefill($newimage, 0, 0, $white);
            imagecopy($newimage, $this->canvas, ($whereWidth == 'right' ? 0 : $addToWidth), ($whereHeight == 'bottom' ? 0 : $addToHeight), 0, 0, $oldw, $oldh);
            $this->canvas = $newimage;
            return true;
        }

        public function addText($text, $x, $y, $w, $h, $alignX='left', $alignY='center', $size=12, array $color=null, $font='opensans') {
            if (is_array($color) === true && count($color) == 3) {
                $color = new \GDText\Color($color[0], $color[1], $color[2]);
            } elseif ($color === null || $color === false) {
                $color = new \GDText\Color(0, 0, 0);
            }
            if (!($color instanceof \GDText\Color)) {
                return false;
            }

            $box = new \GDText\Box($this->canvas);
            $box->setFontFace(FONT_PATH.$font.'.ttf');
            $box->setFontColor($color);
            $box->setFontSize($size);
            $box->setBox($x, $y, $w, $h);
            $box->setTextAlign($alignX, $alignY);
            $box->draw($text);
            return true;
        }
        public function getCanvas() {
            return $this->canvas;
        }
        function scale($byWidth=false, $byHeight=false) {
            $dimensions = $this->getCanvasSize($this->canvas);
            $height = $dimensions['height'];
            $width = $dimensions['height'];

            $newHeight = $height;
            $newWidth = $width;
            if ($byHeight !== false) {
                $newWidth = ($height / $width) * $byHeight;
            }
            if ($byWidth !== false) {
                $newHeight = ($height / $width) * $byWidth;
            }
            $tmp = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            imagecopyresampled($tmp, $this->canvas, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            $this->canvas = $tmp;
        }
    }
?>