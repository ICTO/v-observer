<?php

namespace App\Export;

use App\Export\ExportInterface;

class Excel implements ExportInterface {

    static $row;

    /**
     * {@inheritdoc}
     */
    static function getHumanName(){
        return 'Microsoft Excel (xslx)';
    }

    /**
     * {@inheritdoc}
     */
    static function exportFile($parts, $video, $questionnaire){
        $excel = new \PHPExcel();
        $excel->getProperties()->setCreator($video->creator()->get()->first()->name)
                               ->setTitle("Analysis of ". $video->name . ' ('.$questionnaire->name.')');

        foreach($parts as $p => $part){
            $excel->createSheet($p);
            $excel->setActiveSheetIndex($p);
            $excel->getActiveSheet()->setTitle('Section '.$p);

            self::$row = 1;
            self::processBlocks($part, $excel);

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="analysis-video-'.$video->id.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    private static function processBlocks($blocks, $excel){
        foreach ($blocks as $block) {
            $excel->getActiveSheet()->setCellValue('A'.self::$row , $block['text']);
            if($block['type'] == 'Group'){
                $excel->getActiveSheet()->getStyle('A'.self::$row)->getFont()->setBold(true);
            }
            if(!empty($block['answer'])){
                $excel->getActiveSheet()->setCellValue('B'.self::$row, $block['answer']);
            }
            if(isset($block['score'])){
                $excel->getActiveSheet()->setCellValue('C'.self::$row, $block['score']);
            }

            self::$row++;

            if(!empty($block['childs'])){
                self::processBlocks($block['childs'], $excel);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    static function getContentType(){
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

}
