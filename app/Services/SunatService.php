<?php

namespace App\Services;

use App\Models\Company as ModelsCompany;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Storage;

class SunatService
{
    public function getSee($company)
    {

        $see = new See();
        $see->setCertificate(Storage::get($company->cert_path));
        $see->setService($company->production ? SunatEndpoints::FE_PRODUCCION : SunatEndpoints::FE_BETA);
        $see->setClaveSOL($company->ruc, $company->sol_user, $company->sol_pass);

        return $see;
    }

    public function getInvoice($data)
    {
        // Venta
        return (new Invoice())
            ->setUblVersion($data['ublVersion'] ?? '2.1')
            ->setTipoOperacion($data['tipoOpperacion'] ?? null) // Venta - Catalog. 51
            ->setTipoDoc($data['tipoDoc']?? null) // Factura - Catalog. 01
            ->setSerie($data['serie']?? null)
            ->setCorrelativo($data['correlativo']?? null)
            ->setFechaEmision(new DateTime($data['fechaEmision'] ?? null)) // Zona horaria: Lima
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda($data['TipoMoneda'] ?? null) // Sol - Catalog. 02
            ->setCompany($this->getCompany($data['company']))
            ->setClient($this->getClient($data['client']))


            ->setMtoOperGravadas($data['mtoOperGravadas'] ?? null)
            ->setMtoOperExoneradas($data['mtoOperExoneradas'] ?? null)
            ->setMtoOperInafectas($data['mtoOperInafectas'] ?? null)
            ->setMtoOperExportacion($data['mtoOperExportacion'] ?? null)
            ->setMtoOperGratuitas($data['mtoOperGratuitas'] ?? null)


            ->setMtoIGV($data['mtoIGV'])
            ->setMtoIGVGratuitas($data['mtoIgvGratuitas'])
            ->setIcbper($data['icbper'])
            ->setTotalImpuestos($data['totalImpuestos'])


            ->setValorVenta($data['valorVenta'])
            ->setSubTotal($data['subTotal'])
            ->setRedondeo($data['redondeo'])
            ->setMtoImpVenta($data['mtoImpVenta'])


            ->setDetails($this->getDetails($data['details']))
            ->setLegends($this->getLegends($data['legends']));


    }

    public function getNote($data)
    {
        return (new Note)
        ->setUblVersion($data['ublVersion'] ?? '2.1')
        ->setTipoDoc($data['tipoDoc']?? null) // Factura - Catalog. 01
        ->setSerie($data['serie']?? null)
        ->setCorrelativo($data['correlativo']?? null)
        ->setFechaEmision(new DateTime($data['fechaEmision'] ?? null)) // Zona horaria: Lima
        //->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
        ->setTipDocAfectado($data['tipDocAfectado'] ?? null)
        ->setNumDocfectado($data['numDocAfectado'] ?? null)
        ->setCodMotivo($data['codMotivo'] ?? null)
        ->setDesMotivo($data['desMotivo'] ?? null)
        ->setTipoMoneda($data['tipoMoneda'] ?? null)
        ->setCompany($this->getCompany($data['company']))
        ->setClient($this->getClient($data['client']))

        ->setMtoOperGravadas($data['mtoOperGravadas'] ?? null)
            ->setMtoOperExoneradas($data['mtoOperExoneradas'] ?? null)
            ->setMtoOperInafectas($data['mtoOperInafectas'] ?? null)
            ->setMtoOperExportacion($data['mtoOperExportacion'] ?? null)
            ->setMtoOperGratuitas($data['mtoOperGratuitas'] ?? null)


            ->setMtoIGV($data['mtoIGV'])
            ->setMtoIGVGratuitas($data['mtoIgvGratuitas'])
            ->setIcbper($data['icbper'])
            ->setTotalImpuestos($data['totalImpuestos'])


            ->setValorVenta($data['valorVenta'])
            ->setSubTotal($data['subTotal'])
            ->setRedondeo($data['redondeo'])
            ->setMtoImpVenta($data['mtoImpVenta'])


            ->setDetails($this->getDetails($data['details']))
            ->setLegends($this->getLegends($data['legends']));
    }


    public function getCompany($company)
    {
        return (new Company())
            ->setRuc($company['ruc'] ?? null)
            ->setRazonSocial($company['razonSocial'] ?? null)
            ->setNombreComercial($company['nombreComercial'] ?? null)
            ->setAddress($this->getAddress($company['address']));
    }

    public function getClient($client)
    {
        return (new Client())
            ->setTipoDoc($client['tipoDoc'] ?? null)
            ->setNumDoc($client['numDoc'] ?? null)
            ->setRznSocial($client['rznSocial'] ?? null);
    }

    public function getAddress($address)
    {
        return (new Address())
            ->setUbigueo($address['ubigeo'] ?? null)
            ->setDepartamento($address['departamento'] ?? null)
            ->setProvincia($address['provincia'] ?? null)
            ->setDistrito($address['distrito'] ?? null)
            ->setUrbanizacion($address['urbanizacion'] ?? null)
            ->setDireccion($address['direccion'] ?? null)
            ->setCodLocal($address['codLocal'] ?? null); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

    }

    public function getDetails($details)
    {

        $green_details = [];

        foreach ($details as $detail) {
        $green_details[] = (new SaleDetail())
            ->setTipAfeIgv($detail['tipAfeIgv'] ?? null) // Gravado Op. Onerosa - Catalog. 07
            ->setCodProducto($detail['codProducto'] ?? null)
            ->setUnidad($detail['unidad'] ?? null) // Unidad - Catalog. 03
            ->setDescripcion($detail['descripcion'] ?? null)
            ->setCantidad($detail['cantidad'] ?? null)
            ->setMtoValorUnitario($detail['mtoValorUnitario'] ?? null)
            ->setMtoBaseIgv($detail['mtoBaseIgv'] ?? null)
            ->setPorcentajeIgv($detail['porcentajeIgv'] ?? null) // 18%
            ->setIgv($detail['igv'] ?? null)
            ->setFactorIcbper($detail['factorIcbper'] ?? null) // 0.3%
            ->setIcbper($detail['icbper'] ?? null)
            ->setTotalImpuestos($detail['totalImpuestos'] ?? null) // Suma de impuestos en el detalle
            ->setMtoValorVenta($detail['mtoValorVenta'] ?? null)
            ->setMtoPrecioUnitario($detail['mtoPrecioUnitarios'] ?? null);
        }

        return $green_details;

    }

    public function getLegends($legends)
    {
        $green_legends = [];

        foreach ($legends as $legend) {

            $green_legends[] = (new Legend())
            ->setCode($legend['code'] ?? null) // Monto en letras - Catalog. 52
            ->setValue($legend['value'] ?? null);
        }
            return $green_legends;
    }

    //Response y Reportes
    public function sunatResponse($result)
    {

        $response['success'] = $result->isSuccess();

        // Verificamos que la conexión con SUNAT fue exitosa.
        if (!$response['success']) {
            // Mostrar error al conectarse a SUNAT.
            $response['error'] = [
                'code' => $result->getError()->getCode(),
                'message' => $result->getError()->getMessage()
            ];
            return $response;
        }

        $response['cdrZip'] = base64_encode($result->getCdrZip());

        $cdr = $result->getCdrResponse();

        $response['cdrResponse'] = [
            'code' => (int)$cdr->getCode(),
            'description' => $cdr->getDescription(),
            'notes' => $cdr->getNotes()
        ];

        return $response;
    }

    public function getHtmlReport($invoice)
    {
        $report = new HtmlReport();

        $resolver = new DefaultTemplateResolver();

        $report->setTemplate($resolver->getTemplate($invoice));

        $ruc = $invoice->getCompany()->getRuc();
        $company = ModelsCompany::where('ruc', $ruc)
                                ->where('user_id', auth()->id())
                                ->first();

        $params = [
            'system' => [
                'logo' => Storage::get($company->logo_path), // Logo de Empresa
                'hash' => 'qqnr2dN4p/HmaEA/CJuVGo7dv5g=', // Valor Resumen
            ],
            'user' => [
                'header'     => 'Telf: <b>(51) 980920380 </b>', // Texto que se ubica debajo de la dirección de empresa
                'extras'     => [
                    // Leyendas adicionales
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'     ],
                    ['name' => 'VENDEDOR'         , 'value' => 'JOSE MENDOZA'],
                ],
                'footer' => '<p>Nro Resolucion: <b>3232323</b></p>'
            ]
        ];
        return $report->render($invoice, $params);

    }

    public function generatePdfReport($invoice)
    {
        $htmlReport = new HtmlReport();

        $resolver = new DefaultTemplateResolver();
        $htmlReport->setTemplate($resolver->getTemplate($invoice));

        $ruc = $invoice->getCompany()->getRuc();
        $company = ModelsCompany::where('ruc', $ruc)
                                ->where('user_id', auth()->id())
                                ->first();

        $report = new PdfReport($htmlReport);

        $report->setOptions( [
            'no-outline',
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',
        ]);

        $report->setBinPath(env('WKHTMLTO_PDF_PATH'));

        $params = [
            'system' => [
                'logo' => Storage::get($company->logo_path), // Logo de Empresa
                'hash' => 'qqnr2dN4p/HmaEA/CJuVGo7dv5g=', // Valor Resumen
            ],
            'user' => [
                'header'     => 'Telf: <b>(51) 980920380 </b>', // Texto que se ubica debajo de la dirección de empresa
                'extras'     => [
                    // Leyendas adicionales
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'     ],
                    ['name' => 'VENDEDOR'         , 'value' => 'JOSE MENDOZA'],
                ],
                'footer' => '<p>Nro Resolucion: <b>3232323</b></p>'
            ]
        ];

        $pdf = $report->render($invoice, $params);

        Storage::put('invoice/' . $invoice->getName() . '.pdf', $pdf);
    }
}
