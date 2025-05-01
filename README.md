Správa produktů – zadání pro krytypodmotor.cz
Tato jednoduchá webová aplikace splňuje požadavky na uložení, zobrazení i úpravu informací o produktech podle specifikace od krytypodmotor.cz.

1. Přehled funkcionality
Datový model

products: id, code (např. PM00014), description, price, brand_id, material_id

brands: id, name (Audi, Ford, Škoda, …)

materials: id, type (Plast, Plech, Hliník)

Cizí klíče z products.brand_id → brands.id a products.material_id → materials.id

Dataset

Naimportováno 20+ produktů s různými kombinacemi značek a materiálů

Uživatelské rozhraní

Výpis dat v tabulce po 10 záznamech

Filtrování (značka, materiál, cenové rozmezí, fulltextový popis)

Řazení podle všech sloupců (code, price, brand, material)

Stránkování s dynamickými odkazy

Export aktuálního výpisu do CSV

Editace libovolného záznamu přímo v tabulce

2. Databázové skripty
V kořenovém adresáři najdete dva SQL soubory:

schema.sql – vytvoření tabulek (brands, materials, products) včetně všech primárních a cizích klíčů

seed.sql – vložení vzorových dat (20 produktů, 5 značek, 4 materiály)

Postup:

Otevřete vaši MySQL konzoli (nebo phpMyAdmin)

Spusťte schema.sql

Spusťte seed.sql

3. Struktura projektu
pgsql
Zkopírovat
Upravit
├── public/  
│   └── index.php           – hlavní vstupní skript  
├── src/  
│   ├── Controllers/        – logika zpracování požadavků  
│   ├── Models/             – třídy mapující tabulky  
│   └── Views/              – šablony HTML + JS/CSS  
├── config/  
│   └── database.php        – nastavení připojení k MySQL  
├── sql/  
│   ├── schema.sql  
│   └── seed.sql  
└── assets/  
    ├── css/  
    └── js/  
4. Rychlý start
Nastavte připojení v config/database.php (host, uživatel, heslo, databáze).

Importujte databázi pomocí schema.sql a seed.sql.

Otevřete ve webovém prohlížeči public/index.php.

5. Technologie
Backend: PHP (bez frameworku)

Databáze: MySQL

Frontend: HTML5, CSS3, Vanilla JavaScript

6. Poznámky pro zadavatele
Aplikace je modulárně rozdělena pro snadnou údržbu a budoucí rozšíření.

Pro nasazení stačí běžné PHP hostingové prostředí s MySQL.

V případě zájmu o implementaci do Laravelu / Symfony / jiného frameworku stačí přidat standardní strukturu „MVC“ – logika zůstane stejná.
