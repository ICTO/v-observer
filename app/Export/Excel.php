<?php

namespace App\Export;

use App\Export\ExportInterface;
use Auth;

class Excel implements ExportInterface {

    static $row;
    static $column;

    /**
     * {@inheritdoc}
     */
    static function getHumanName(){
        return 'Microsoft Excel (xslx)';
    }

    /**
     * {@inheritdoc}
     */
    static function exportFile($analyses){
        $user = Auth::user();
        $excel = new \PHPExcel();
        $questionnaireName = $analyses[0]['analysis']->questionnaire()->get()->first()->name;
        $excel->getProperties()->setCreator($user->name)
                               ->setTitle($questionnaireName);

        foreach($analyses as $r => $row){
            if($r){
                $excel->createSheet();
                $excel->setActiveSheetIndex($r);
            }
            $analysis = $row['analysis'];
            $questionnaire = $analysis->questionnaire()->get()->first();
            $interval = $questionnaire->interval;

            // analysis info
            $creatorName = $analysis->creator()->get()->first()->name;
            $videoName = $analysis->video()->get()->first()->name;
            $excel->getActiveSheet()->setTitle($creatorName);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Questionnaire');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $questionnaireName);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Video');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, 2, $videoName);
            $excel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Creator');
            $excel->getActiveSheet()->setCellValueByColumnAndRow(1, 3, $creatorName);

            self::$column = 0;
            self::$row = 6;
            self::processAnswerText($row['answers'][0], $excel);
            self::$column = 1;

            foreach($row['answers'] as $p => $part){
                self::$row = 5;
                self::processAnswerInterval($p, $interval, $excel);
                self::$row = 6;
                self::processAnswerScores($part, $excel);
                self::$column++;
            }
        }

        if(count($analyses) > 1){
            $filename = str_replace('/', ' - ', $questionnaireName . ' - ' . $videoName);
        } else {
            $filename = str_replace('/', ' - ', $questionnaireName . ' - ' . $videoName . ' - ' . $creatorName);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
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

    private static function processAnswerInterval($p, $interval, $excel){
        $excel->getActiveSheet()->setCellValueByColumnAndRow(self::$column, self::$row, $p*$interval . '->' . ($p+1)*$interval . ' sec');
    }

    private static function processAnswerText($blocks, $excel){
        foreach ($blocks as $block) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow(self::$column, self::$row, $block['text']);
            if($block['type'] == 'Group'){
                $excel->getActiveSheet()->getStyle(self::$column.self::$row)->getFont()->setBold(true);
            }

            self::$row++;

            if(!empty($block['childs'])){
                self::processAnswerText($block['childs'], $excel);
            }
        }
    }

    private static function processAnswerScores($blocks, $excel){
        foreach ($blocks as $block) {
            if(isset($block['score']) && is_numeric($block['score'])){
                $excel->getActiveSheet()->setCellValueByColumnAndRow(self::$column, self::$row, $block['score']);
            } else if(isset($block['answer'])){
                $excel->getActiveSheet()->setCellValueByColumnAndRow(self::$column, self::$row, $block['answer']);
            }

            self::$row++;

            if(!empty($block['childs'])){
                self::processAnswerScores($block['childs'], $excel);
            }
        }
    }

}
