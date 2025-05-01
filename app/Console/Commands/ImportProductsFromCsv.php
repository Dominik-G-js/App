<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProductsFromCsv extends Command
{
    protected $signature = 'products:import {file?}';
    protected $description = 'Import products from CSV file';

    public function handle()
    {
        $file = $this->argument('file') ?? resource_path('csv/produkty.csv');
        
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $this->info("Importing products from $file");

        $content = file_get_contents($file);
        
        $delimiters = [
            "\t" => substr_count($content, "\t"),
            ',' => substr_count($content, ','),
            ';' => substr_count($content, ';')
        ];
        
        arsort($delimiters);
        $delimiter = key($delimiters);
        
        $this->info("Detected delimiter: " . ($delimiter === "\t" ? "TAB" : $delimiter));
        
        $handle = fopen($file, 'r');
        
        $header = fgetcsv($handle, 0, $delimiter);
        if ($header) {
            $this->info("Header: " . implode(' | ', $header));
        } else {
            $this->error("Failed to read header");
            return 1;
        }
        
        $count = 0;
        $errors = 0;

        $firstRow = fgetcsv($handle, 0, $delimiter);
        if ($firstRow) {
            $this->info("First row: " . implode(' | ', $firstRow));

            rewind($handle);

            fgetcsv($handle, 0, $delimiter);
        }
        
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($data) < 5) {
                $this->warn("Neplatný řádek: " . implode(' | ', $data));
                $errors++;
                continue;
            }
            
            try {

                $brandName = trim($data[0]);
                $materialName = trim($data[1]);
                $priceStr = trim($data[2]);
                $description = trim($data[3]);
                $code = trim($data[4]);
                
                $this->info("Processing: Brand=$brandName, Material=$materialName, Price=$priceStr, Code=$code");

                $price = str_replace(' ', '', $priceStr);
                $price = str_replace('Kč', '', $price);
                $price = str_replace(',', '.', $price);
                
                $this->info("Processed price: $price");
                
                if (!is_numeric($price)) {
                    $this->warn("Neplatná cena: $priceStr");
                    $errors++;
                    continue;
                }
                
                $brand = Brand::firstOrCreate(['name' => $brandName]);
                $material = Material::firstOrCreate(['name' => $materialName]);
                
                $exists = DB::table('products')->where('code', $code)->exists();
                
                if ($exists) {
                    DB::table('products')
                        ->where('code', $code)
                        ->update([
                            'brand_id' => $brand->id,
                            'material_id' => $material->id,
                            'price' => $price,
                            'description' => $description,
                            'updated_at' => now()
                        ]);
                    
                    $this->info("Updated product: $code");
                } else {
                    DB::table('products')->insert([
                        'code' => $code,
                        'brand_id' => $brand->id,
                        'material_id' => $material->id,
                        'price' => $price,
                        'description' => $description,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $this->info("Created product: $code");
                }
                
                $count++;
            } catch (\Exception $e) {
                $this->error("Chyba: " . $e->getMessage());
                $this->line("Data: " . implode(' | ', $data));
                $errors++;
            }
        }
        
        fclose($handle);
        
        $this->info("Import dokončen. Úspěšně importováno: $count, Chyb: $errors");
        return 0;
    }
}

