<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

class PDF
{
    /**
     * Load a view and return it as a PDF
     */
    public static function loadView($view, $data = [], $mergeData = [])
    {
        // Simple implementation that just returns the view
        // In production, you would use a PDF library like dompdf, TCPDF, or mPDF
        return new class($view, $data, $mergeData) {
            protected $view;
            protected $data;
            protected $mergeData;
            
            public function __construct($view, $data, $mergeData)
            {
                $this->view = $view;
                $this->data = $data;
                $this->mergeData = $mergeData;
            }
            
            public function download($filename)
            {
                $html = View::make($this->view, $this->data, $this->mergeData)->render();
                
                return response($html)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }
        };
    }
}
