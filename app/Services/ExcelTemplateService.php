<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelTemplateService
{
    /**
     * Columns required in the products Excel file
     */
    public static array $columns = [
        'product_name',
        'slug',
        'root_category',
        'category',
        'subcategory',
        'full_category_path',
        'brand',
        'chemical_name',
        'cas_number',
        'hsn_code',
        'purity',
        'packaging',
        'description',
        'features',
        'applications',
        'specifications',
        'storage_info',
        'image_path',
        'pdf_path',
        'is_featured',
        'sort_order',
        'status'
    ];

    /**
     * Pre-populated sample products covering authoritative category paths
     */
    public static array $sampleRows = [
        [
            'product_name' => 'Nitric Acid',
            'slug' => 'nitric-acid',
            'root_category' => 'Products',
            'category' => 'GACL Products',
            'subcategory' => 'Acid Products',
            'full_category_path' => 'GACL Products > Acid Products',
            'brand' => 'GACL',
            'chemical_name' => 'Nitric Acid (HNO₃)',
            'cas_number' => '7697-37-2',
            'hsn_code' => '28080010',
            'purity' => '68% - 70%',
            'packaging' => '30kg Carboys / Tanker',
            'description' => 'Nitric Acid is a highly corrosive mineral acid used in fertilizer, dye, and chemical production.',
            'features' => 'High Purity, Clear Liquid, Standard Industrial Grade',
            'applications' => 'Fertilizer production, Dye industry, Explosives, Metal etching',
            'specifications' => '{"Purity":"68% Min","Specific Gravity":"1.41","Boiling Point":"121°C"}',
            'storage_info' => 'Store in cool, well-ventilated, acid-resistant area away from direct sunlight.',
            'image_path' => 'C:\\xampp\\htdocs\\SR\\assets\\img\\added\\product\\nitric-acid.jpg',
            'pdf_path' => 'C:\\xampp\\htdocs\\SR\\assets\\pdf\\MSDC\\nitric-acid.pdf',
            'is_featured' => 1,
            'sort_order' => 1,
            'status' => 1
        ],
        [
            'product_name' => 'Caustic Soda Flakes (NaOH)',
            'slug' => 'caustic-soda-flakes-naoh',
            'root_category' => 'Products',
            'category' => 'GACL Products',
            'subcategory' => 'Chlor-Alkali Chemicals',
            'full_category_path' => 'GACL Products > Chlor-Alkali Chemicals',
            'brand' => 'GACL',
            'chemical_name' => 'Sodium Hydroxide (NaOH)',
            'cas_number' => '1310-73-2',
            'hsn_code' => '28151110',
            'purity' => '99.0% Min',
            'packaging' => '50kg HDPE Bags',
            'description' => 'Caustic Soda Flakes is an essential strong base chemical used in paper, soap, and chemical manufacture.',
            'features' => 'High Purity Flakes, Fast Dissolving, Premium Grade',
            'applications' => 'Soap & Detergents, Textile Processing, Paper Mills, Water Treatment',
            'specifications' => '{"Assay (NaOH)":"99.0% Min","Sodium Carbonate":"0.4% Max","Chloride (NaCl)":"0.1% Max"}',
            'storage_info' => 'Store in tightly closed bags in dry area. Protect from moisture.',
            'image_path' => 'C:\\xampp\\htdocs\\SR\\assets\\img\\added\\product\\Caustic-Soda-Flakes-NaOH.jpg',
            'pdf_path' => 'C:\\xampp\\htdocs\\SR\\assets\\pdf\\MSDC\\caustic-soda-flakes.pdf',
            'is_featured' => 1,
            'sort_order' => 2,
            'status' => 1
        ],
        [
            'product_name' => 'Nitric Acid',
            'slug' => 'gnfc-nitric-acid',
            'root_category' => 'Products',
            'category' => 'GNFC Products',
            'subcategory' => 'Specialty Chemicals & Urea',
            'full_category_path' => 'GNFC Products > Specialty Chemicals & Urea',
            'brand' => 'GNFC',
            'chemical_name' => 'Nitric Acid Technical Grade',
            'cas_number' => '7697-37-2',
            'hsn_code' => '28080010',
            'purity' => '60% Grade',
            'packaging' => 'Tanker Supply',
            'description' => 'GNFC High Purity Nitric Acid engineered for industrial chemical syntheses and specialized applications.',
            'features' => 'GNFC Standard, Concentrated Formula, Heavy Industrial Grade',
            'applications' => 'Industrial Synthesis, Urea derivatives, Agro-chemicals',
            'specifications' => '{"Purity":"60% Min","Iron (Fe)":"5 ppm Max","Chloride":"10 ppm Max"}',
            'storage_info' => 'Store in dedicated SS/Glass lined tanks with secondary containment.',
            'image_path' => 'C:\\xampp\\htdocs\\SR\\assets\\img\\added\\product\\nitric-acid.jpg',
            'pdf_path' => 'C:\\xampp\\htdocs\\SR\\assets\\pdf\\MSDC\\nitric-acid.pdf',
            'is_featured' => 0,
            'sort_order' => 3,
            'status' => 1
        ],
        [
            'product_name' => 'Mono Chloro Benzene (MCB)',
            'slug' => 'mono-chloro-benzene-mcb',
            'root_category' => 'Products',
            'category' => 'Organic Products',
            'subcategory' => 'Chlorobenzenes',
            'full_category_path' => 'Organic Products > Chlorobenzenes',
            'brand' => 'SRCIL',
            'chemical_name' => 'Chlorobenzene (C₆H₅Cl)',
            'cas_number' => '108-90-7',
            'hsn_code' => '29039110',
            'purity' => '99.5% Min',
            'packaging' => '200kg MS Drums',
            'description' => 'Mono Chloro Benzene is an organic solvent used as an intermediate in dye and pharmaceutical production.',
            'features' => 'Clear Colorless Liquid, High Purity Organic Intermediate',
            'applications' => 'Agrochemicals, Pharmaceutical Intermediates, Solvent, Rubber chemicals',
            'specifications' => '{"Purity":"99.5% Min","Moisture":"0.05% Max","Benzene":"0.1% Max"}',
            'storage_info' => 'Keep in flameproof room. Away from sparks and open flames.',
            'image_path' => 'C:\\xampp\\htdocs\\SR\\assets\\img\\added\\product\\Mono-Chloro-Benzene-MCB.jpg',
            'pdf_path' => 'C:\\xampp\\htdocs\\SR\\assets\\pdf\\MSDC\\mono-chloro-benzene.pdf',
            'is_featured' => 0,
            'sort_order' => 4,
            'status' => 1
        ],
        [
            'product_name' => 'Borax Decahydrate',
            'slug' => 'borax-decahydrate',
            'root_category' => 'Products',
            'category' => 'DMCC Products',
            'subcategory' => 'Boron Chemicals',
            'full_category_path' => 'DMCC Products > Boron Chemicals',
            'brand' => 'DMCC',
            'chemical_name' => 'Disodium Tetraborate Decahydrate (Na₂B₄O₇·10H₂O)',
            'cas_number' => '1303-96-4',
            'hsn_code' => '28401100',
            'purity' => '99.5% Technical',
            'packaging' => '25kg HDPE Bags',
            'description' => 'Borax Decahydrate is an important boron compound used in glass, ceramics, and detergent manufacturing.',
            'features' => 'Crystalline Powder, Premium Boron Content, High Solubility',
            'applications' => 'Glass Fiber, Ceramic Glazes, Detergents, Agriculture Flux',
            'specifications' => '{"B2O3 Content":"36.5% Min","Purity":"99.5% Min","Insolubles":"0.05% Max"}',
            'storage_info' => 'Store in dry place avoiding atmospheric moisture and cake formation.',
            'image_path' => 'C:\\xampp\\htdocs\\SR\\assets\\img\\added\\product\\Borax-Decahydrate.jpg',
            'pdf_path' => 'C:\\xampp\\htdocs\\SR\\assets\\pdf\\MSDC\\borax-decahydrate.pdf',
            'is_featured' => 0,
            'sort_order' => 5,
            'status' => 1
        ]
    ];

    /**
     * Generate spreadsheet object
     */
    public function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products Migration');

        // Header Styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'] // Dark Slate
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '0F172A']]
            ]
        ];

        // Write Header Row
        $colIndex = 1;
        foreach (static::$columns as $column) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '1', $column);
            $colIndex++;
        }
        $sheet->getStyle('A1:V1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Write Sample Data Rows
        $rowIndex = 2;
        foreach (static::$sampleRows as $row) {
            $colIndex = 1;
            foreach (static::$columns as $column) {
                $val = $row[$column] ?? '';
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowIndex, $val);
                $colIndex++;
            }
            $sheet->getRowDimension($rowIndex)->setRowHeight(22);
            $rowIndex++;
        }

        // Auto-fit column widths
        foreach (range('A', 'V') as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Export Excel template to path or stream output
     */
    public function generateFile(string $outputPath): void
    {
        $spreadsheet = $this->buildSpreadsheet();
        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);
    }
}
