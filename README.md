# Správa produktů - Dokumentace projektu

Tento projekt je webová aplikace pro správu produktů vytvořená v PHP frameworku Laravel. Aplikace umožňuje filtrování, řazení, stránkování a export produktů.

## Obsah

1. [Struktura projektu](#struktura-projektu)
2. [Klíčové funkce](#klíčové-funkce)
3. [Implementace filtrování](#implementace-filtrování)
4. [Implementace řazení](#implementace-řazení)
5. [Implementace stránkování](#implementace-stránkování)
6. [Export dat](#export-dat)
7. [Frontend](#frontend)
8. [Často kladené otázky (FAQ)](#často-kladené-otázky-faq)

## Struktura projektu

Projekt využívá standardní strukturu Laravel aplikace:

- `app/Http/Controllers/ProductController.php` - Hlavní kontroler pro správu produktů
- `app/Models/Product.php` - Model produktu
- `app/Models/Brand.php` - Model značky
- `app/Models/Material.php` - Model materiálu
- `resources/views/products/index.blade.php` - Hlavní šablona pro zobrazení seznamu produktů
- `resources/views/products/edit.blade.php` - Šablona pro úpravu produktu
- `routes/web.php` - Definice routování

## Klíčové funkce

### Filtrování produktů

Aplikace umožňuje filtrovat produkty podle:
- Značky
- Materiálu
- Cenového rozpětí (minimální a maximální cena)
- Textového vyhledávání (kód nebo popis)

### Řazení produktů

Produkty lze řadit podle:
- ID
- Kódu
- Značky
- Materiálu
- Ceny

### Stránkování

Implementováno dynamické stránkování s možností přechodu na konkrétní stránku.

### Export dat

Možnost exportu filtrovaných dat do CSV souboru.

## Implementace filtrování

Filtrování je implementováno v metodě `index` v `ProductController.php`:

```php
// Filtrování
if ($request->has('brand_id') && $request->brand_id) {
    $query->where('brand_id', $request->brand_id);
}

if ($request->has('material_id') && $request->material_id) {
    $query->where('material_id', $request->material_id);
}

// Filtrování podle cenového rozpětí
if ($request->has('min_price') && is_numeric($request->min_price)) {
    $query->where('price', '>=', $request->min_price);
}

if ($request->has('max_price') && is_numeric($request->max_price)) {
    $query->where('price', '<=', $request->max_price);
}

if ($request->has('search') && $request->search) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('code', 'like', "%$search%")
          ->orWhere('description', 'like', "%$search%");
    });
}
```

## Implementace řazení

Řazení je implementováno v metodě `index` v `ProductController.php`:

```php
// Řazení
$sortField = $request->sort_by ?? 'id';
$sortDirection = $request->sort_direction ?? 'asc';
$query->orderBy($sortField, $sortDirection);
```

Na frontendu je řazení implementováno pomocí JavaScriptu, který reaguje na kliknutí na záhlaví tabulky.

## Implementace stránkování

Stránkování je implementováno pomocí Laravel Pagination:

```php
// Stránkování
$products = $query->paginate(10);
```

V šabloně je pak implementováno vlastní zobrazení stránkování, které umožňuje přechod na konkrétní stránku.

## Export dat

Export dat je implementován v metodě `export` v `ProductController.php`:

```php
public function export(Request $request)
{
    $query = Product::with(['brand', 'material']);

    // Aplikujeme stejné filtry jako pro zobrazení
    // ...

    $products = $query->get();

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="produkty.csv"',
    ];

    $callback = function() use ($products) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['Značka', 'Materiál', 'Kód', 'Cena', 'Popis']);

        foreach ($products as $product) {
            fputcsv($file, [
                $product->brand->name,
                $product->material->name,
                $product->code,
                $product->price,
                $product->description,
            ]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}
```

## Frontend

Frontend je implementován pomocí HTML, CSS a JavaScriptu. Pro lepší uživatelský zážitek jsou použity:
- Responzivní design
- Interaktivní prvky (řazení, stránkování)
- Vizuální zpětná vazba (zvýraznění aktivních filtrů)
- Font Awesome ikony

## Často kladené otázky (FAQ)

### Proč je použita podmínka `if ($request->has('brand_id') && $request->brand_id)`?

Tato podmínka má dva účely:
1. `$request->has('brand_id')` - Kontroluje, zda byl parametr `brand_id` vůbec odeslán ve formuláři
2. `$request->brand_id` - Kontroluje, zda hodnota parametru není prázdná

Tato dvojitá kontrola zajišťuje, že filtr bude aplikován pouze v případě, že uživatel skutečně vybral nějakou značku. Pokud by byla použita pouze druhá část podmínky, mohlo by dojít k chybě, pokud by parametr nebyl vůbec odeslán.

### Proč je použit `$query->where(function($q) use ($search) { ... })`?

Tato konstrukce umožňuje vytvořit komplexní podmínku WHERE s použitím OR. V tomto případě hledáme produkty, které mají hledaný text buď v kódu NEBO v popisu. Použití closure funkce zajišťuje, že tyto podmínky budou správně seskupeny v SQL dotazu.

### Proč je použit `$products->appends(request()->except('page'))->links()`?

Tato metoda zajišťuje, že při přechodu na jinou stránku budou zachovány všechny aktuální filtry a řazení. Metoda `appends()` přidává do URL parametry, které jsou potřeba pro zachování aktuálního stavu aplikace. Parametr `page` je vyloučen, protože ten je automaticky přidán metodou `links()`.

### Proč je použit `Response::stream()` pro export CSV?

Metoda `Response::stream()` umožňuje streamovat data přímo do prohlížeče bez nutnosti ukládat soubor na serveru. To je efektivnější zejména pro větší soubory, protože:
1. Nespotřebovává diskový prostor na serveru
2. Začíná odesílat data okamžitě, bez čekání na vygenerování celého souboru
3. Využívá méně paměti, protože data jsou zpracovávána postupně

### Proč je použit `{{ $products->firstItem() ?? 0 }}` v zobrazení stránkování?

Tato konstrukce zobrazuje číslo prvního zobrazeného záznamu na aktuální stránce. Operátor `??` (null coalescing) zajišťuje, že pokud je výsledek `null` (například když nejsou žádné výsledky), zobrazí se hodnota 0. Tím se předchází chybám při zobrazení.

### Proč je použit grid layout místo flexboxu pro formulář?

Grid layout poskytuje lepší kontrolu nad rozložením prvků ve dvou dimenzích. V tomto případě umožňuje:
1. Konzistentní šířku prvků v rámci sloupce
2. Automatické zarovnání prvků do řádků a sloupců
3. Lepší responzivitu na různých velikostech obrazovky

### Proč je použit vlastní JavaScript pro řazení místo nativního řazení v Laravel?

Vlastní JavaScript pro řazení umožňuje:
1. Okamžitou zpětnou vazbu uživateli (šipky indikující směr řazení)
2. Řazení bez nutnosti implementovat složitou logiku na straně serveru
3. Lepší uživatelský zážitek, protože není potřeba přidávat další prvky do UI

### Proč je použit `number_format($product->price, 2, ',', ' ')` pro zobrazení ceny?

Tato funkce formátuje číslo do čitelného formátu:
1. Zobrazuje vždy 2 desetinná místa
2. Používá čárku jako oddělovač desetinných míst (standard v češtině)
3. Používá mezeru jako oddělovač tisíců pro lepší čitelnost větších čísel

### Proč je použit `Str::limit($product->description, 50)` pro zobrazení popisu?

Tato metoda omezuje délku textu na 50 znaků a přidává tři tečky na konec, pokud je text delší. To zajišťuje konzistentní zobrazení v tabulce a zabraňuje "rozbití" layoutu příliš dlouhým textem.

### Proč je použit `with(['brand', 'material'])` při načítání produktů?

Tato metoda implementuje tzv. "eager loading" v Eloquent ORM. Místo toho, aby se pro každý produkt prováděl samostatný SQL dotaz pro načtení značky a materiálu (což by vedlo k N+1 problému), načtou se všechny potřebné značky a materiály v jednom dotazu. To výrazně zlepšuje výkon aplikace, zejména při zobrazení většího množství produktů.

### Proč je použit `request()->except('page')` v metodě `appends()`?

Tato konstrukce zajišťuje, že všechny parametry z aktuálního požadavku (kromě parametru 'page') budou zachovány při generování odkazů na stránkování. To je důležité, protože chceme zachovat všechny aktivní filtry a řazení při přechodu mezi stránkami, ale nechceme duplikovat parametr 'page', který je již automaticky přidáván metodou `links()`.

### Proč je použita metoda `is_numeric()` při kontrole cenového rozpětí?

Tato metoda zajišťuje, že hodnota zadaná uživatelem je skutečně číslo. Je to důležitá bezpečnostní kontrola, která zabraňuje SQL injection a jiným typům útoků. Zároveň zajišťuje, že do databázového dotazu se dostanou pouze platné číselné hodnoty, což předchází chybám při vykonávání dotazu.

### Proč je použit `nullable()` v migracích pro některé sloupce?

Metoda `nullable()` v migracích označuje, že sloupec může obsahovat hodnotu NULL. To je užitečné pro sloupce, které nejsou povinné. V kontextu této aplikace to může být například u filtrů, kde uživatel nemusí vždy zadat všechny hodnoty.

### Jak funguje dynamické stránkování v aplikaci?

Dynamické stránkování je implementováno pomocí vlastního kódu v šabloně, který:
1. Zobrazuje pouze omezený počet stránek kolem aktuální stránky
2. Přidává tlačítka pro přechod na první a poslední stránku
3. Zobrazuje elipsy (...) pro indikaci vynechaných stránek
4. Automaticky se přizpůsobuje celkovému počtu stránek

Toto řešení je uživatelsky přívětivější než standardní stránkování, zejména když je celkový počet stránek velký.

### Proč je použit Font Awesome pro ikony místo obrázků?

Font Awesome poskytuje vektorové ikony, které:
1. Jsou škálovatelné bez ztráty kvality
2. Lze je snadno stylovat pomocí CSS (barva, velikost, stíny)
3. Načítají se rychleji než jednotlivé obrázkové soubory
4. Poskytují konzistentní vzhled napříč celou aplikací

### Jak je zajištěna bezpečnost aplikace?

Aplikace implementuje několik bezpečnostních mechanismů:
1. Validace vstupních dat na straně serveru
2. Ochrana proti SQL injection pomocí parametrizovaných dotazů (Laravel Query Builder)
3. CSRF ochrana pro formuláře
4. XSS ochrana pomocí escapování výstupu v šablonách
5. Typová kontrola vstupních dat (např. `is_numeric()` pro cenové rozpětí)

### Proč jste nepoužili repository pattern místo přímého volání modelů v kontroleru?

Pro tento projekt jsem se rozhodl nepoužít repository pattern z několika důvodů:
1. Jedná se o menší aplikaci s jednoduchým datovým modelem
2. Laravel Eloquent již poskytuje dostatečnou abstrakci nad databází
3. Přidání další vrstvy by v tomto případě zvýšilo komplexitu bez významného přínosu
4. Kód je i bez repository patternu dobře testovatelný a udržovatelný

Pro větší aplikace s komplexnějšími datovými operacemi by repository pattern byl vhodnější volbou.

### Proč jste použili vlastní CSS místo frameworku jako Bootstrap nebo Tailwind?

Rozhodl jsem se pro vlastní CSS z několika důvodů:
1. Aplikace má specifické požadavky na design, které by vyžadovaly značné přizpůsobení jakéhokoliv frameworku
2. Vlastní CSS poskytuje lepší kontrolu nad výsledným kódem a jeho velikostí
3. Eliminuje se závislost na externích knihovnách, což zjednodušuje údržbu
4. Pro tento konkrétní projekt je množství potřebného CSS relativně malé

Nicméně v produkčním prostředí by bylo vhodné zvážit použití CSS preprocessoru jako SASS nebo LESS pro lepší organizaci stylů.

### Jak byste implementovali vyhledávání produktů podle více kritérií najednou?

Pro implementaci komplexnějšího vyhledávání bych použil následující přístup:
1. Vytvořil bych samostatnou třídu `ProductFilter`, která by zapouzdřila logiku filtrování
2. Implementoval bych metodu `apply()`, která by přijímala query builder a aplikovala na něj všechny filtry
3. Pro každý typ filtru bych vytvořil samostatnou metodu, což by zlepšilo udržovatelnost kódu
4. Použil bych návrhový vzor Chain of Responsibility pro postupné aplikování filtrů

```php
class ProductFilter
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($query)
    {
        if ($this->request->has('brand_id') && $this->request->brand_id) {
            $query = $this->filterByBrand($query);
        }

        // Aplikace dalších filtrů...

        return $query;
    }

    protected function filterByBrand($query)
    {
        return $query->where('brand_id', $this->request->brand_id);
    }

    // Další metody pro filtrování...
}
```

### Jak byste optimalizovali výkon aplikace při velkém množství dat?

Pro optimalizaci výkonu bych implementoval následující strategie:
1. **Indexy v databázi** - Přidání indexů na sloupce používané pro filtrování a řazení (brand_id, material_id, price, code)
2. **Cachování** - Implementace cachování pro často používané dotazy pomocí Laravel Cache
3. **Lazy loading vs. Eager loading** - Strategické použití eager loadingu pro redukci počtu databázových dotazů
4. **Paginace** - Omezení počtu záznamů načítaných najednou (již implementováno)
5. **Optimalizace dotazů** - Použití nástrojů jako Laravel Debugbar pro identifikaci a optimalizaci pomalých dotazů
6. **Komprese a minifikace** - Komprese CSS a JS souborů pro rychlejší načítání stránky

```php
// Příklad implementace cachování
public function index(Request $request)
{
    $cacheKey = 'products_' . md5(json_encode($request->all()));

    return Cache::remember($cacheKey, now()->addMinutes(30), function() use ($request) {
        // Existující logika pro získání produktů
    });
}
```

### Jak byste implementovali unit testy pro tuto aplikaci?

Pro testování bych použil PHPUnit a následující strategii:
1. **Unit testy** pro modely a samostatné komponenty
2. **Feature testy** pro testování celých endpointů a jejich chování
3. **Browser testy** pomocí Laravel Dusk pro testování JavaScript funkcionality

Příklad unit testu pro filtrování produktů:

```php
public function testProductFilteringByBrand()
{
    // Arrange
    $brand = Brand::factory()->create();
    $product1 = Product::factory()->create(['brand_id' => $brand->id]);
    $product2 = Product::factory()->create();

    // Act
    $response = $this->get('/products?brand_id=' . $brand->id);

    // Assert
    $response->assertSee($product1->code);
    $response->assertDontSee($product2->code);
}
```

### Proč jste nepoužili API a frontend framework jako React nebo Vue.js?

Rozhodnutí nepoužít oddělené API a frontend framework bylo založeno na:
1. **Jednoduchosti projektu** - Pro tento typ aplikace by oddělení frontendu a backendu přineslo zbytečnou komplexitu
2. **Rychlosti vývoje** - Blade šablony poskytují rychlý způsob vytváření dynamických stránek
3. **SEO** - Server-side rendering je výhodnější pro SEO než SPA aplikace
4. **Uživatelské zkušenosti** - Pro tento typ aplikace není potřeba bohatá interaktivita, kterou poskytují JS frameworky

V budoucnu by bylo možné implementovat API endpointy a postupně přecházet na modernější frontend, pokud by to požadavky vyžadovaly.

### Jak byste řešili lokalizaci aplikace do více jazyků?

Pro implementaci vícejazyčné podpory bych použil:
1. **Laravel Localization** - Využití vestavěných funkcí pro překlady
2. **Jazykové soubory** - Vytvoření souborů s překlady pro každý podporovaný jazyk
3. **Middleware** - Implementace middleware pro detekci a nastavení jazyka
4. **Přepínač jazyků** - Přidání UI prvku pro změnu jazyka

```php
// Příklad použití v šabloně
<h1>{{ __('products.title') }}</h1>

// Soubor resources/lang/cs/products.php
return [
    'title' => 'Správa produktů',
    'filter' => 'Filtrovat',
    'reset' => 'Resetovat',
    // další překlady...
];
```

### Jak byste implementovali systém oprávnění pro různé typy uživatelů?

Pro správu oprávnění bych implementoval:
1. **Role a oprávnění** - Vytvoření modelů pro role (např. admin, editor, viewer) a specifická oprávnění
2. **Middleware** - Kontrola oprávnění před přístupem k určitým akcím
3. **Policy třídy** - Definice pravidel pro přístup k jednotlivým zdrojům
4. **Gates** - Centralizovaná definice pravidel pro oprávnění

```php
// Příklad Policy třídy
class ProductPolicy
{
    public function update(User $user, Product $product)
    {
        return $user->hasRole('admin') || $user->hasRole('editor');
    }

    public function delete(User $user, Product $product)
    {
        return $user->hasRole('admin');
    }
}

// Použití v kontroleru
public function update(Request $request, Product $product)
{
    $this->authorize('update', $product);
    // Logika aktualizace...
}
```

### Jak byste implementovali audit trail pro sledování změn v produktech?

Pro sledování změn bych vytvořil:
1. **Model ActivityLog** - Pro ukládání informací o změnách
2. **Observer** - Pro automatické zachycení změn v modelech
3. **Middleware** - Pro zachycení informací o uživateli, který změnu provedl
4. **UI pro zobrazení historie** - Rozhraní pro prohlížení historie změn

```php
// Příklad implementace v ProductObserver
public function updated(Product $product)
{
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'update',
        'model_type' => 'Product',
        'model_id' => $product->id,
        'old_values' => json_encode($product->getOriginal()),
        'new_values' => json_encode($product->getChanges()),
    ]);
}
```

### Proč jste použili Laravel místo jiných PHP frameworků jako Symfony nebo CodeIgniter?

Laravel jsem zvolil z několika důvodů:
1. **Expresivní syntax** - Laravel poskytuje čistou a intuitivní syntax, která zrychluje vývoj
2. **Bohatý ekosystém** - Rozsáhlá knihovna balíčků a nástrojů (Eloquent ORM, Blade, Artisan)
3. **Aktivní komunita** - Velká komunita vývojářů a rozsáhlá dokumentace
4. **Moderní přístup** - Laravel implementuje moderní PHP praktiky a návrhové vzory
5. **Elegantní řešení běžných úkolů** - Autentizace, routing, cachování a další funkce jsou jednoduše implementovatelné

Symfony by byl vhodnější pro větší enterprise aplikace, zatímco CodeIgniter je lehčí, ale neposkytuje tolik funkcí jako Laravel.

### Jak byste implementovali real-time aktualizace v aplikaci?

Pro implementaci real-time funkcí bych použil:
1. **WebSockets** - Pro obousměrnou komunikaci mezi serverem a klientem
2. **Laravel Echo** - Pro integraci s Laravel Event Broadcasting
3. **Pusher nebo Socket.io** - Jako WebSocket server
4. **JavaScript na straně klienta** - Pro zpracování příchozích událostí

```php
// Příklad Event třídy
class ProductUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function broadcastOn()
    {
        return new Channel('products');
    }
}

// Volání v kontroleru
event(new ProductUpdated($product));
```

### Jak byste řešili import velkého množství dat do aplikace?

Pro efektivní import dat bych implementoval:
1. **Queue Jobs** - Zpracování importu na pozadí pomocí Laravel Queue
2. **Chunking** - Rozdělení velkých souborů na menší části pro postupné zpracování
3. **Batch Processing** - Použití Laravel Batch pro sledování průběhu importu
4. **Progress Tracking** - Informování uživatele o průběhu importu
5. **Validace** - Kontrola dat před importem a logování chyb

```php
// Příklad implementace importu
public function import(Request $request)
{
    $file = $request->file('import_file');
    $path = $file->store('imports');

    Bus::batch([
        new ImportProductsJob($path)
    ])->dispatch();

    return back()->with('message', 'Import byl zahájen a bude zpracován na pozadí.');
}
```

### Jak byste implementovali vyhledávání s našeptávačem (autocomplete)?

Pro implementaci našeptávače bych použil:
1. **AJAX** - Pro asynchronní dotazy na server
2. **Debouncing** - Pro omezení počtu požadavků při psaní
3. **JSON API Endpoint** - Pro vracení výsledků vyhledávání
4. **JavaScript knihovnu** - Např. jQuery UI Autocomplete nebo vlastní implementaci

```php
// API endpoint pro našeptávač
public function autocomplete(Request $request)
{
    $search = $request->get('query');
    $products = Product::where('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->limit(10)
                      ->get(['id', 'code', 'description']);

    return response()->json($products);
}
```

### Jak byste řešili verzování API v Laravel aplikaci?

Pro verzování API bych použil:
1. **URL prefix** - Např. `/api/v1/products`
2. **Namespace** - Organizace kontrolerů podle verzí
3. **API Resources** - Pro konzistentní formátování odpovědí
4. **Dokumentaci** - Použití nástrojů jako Swagger pro dokumentaci API

```php
// routes/api.php
Route::prefix('v1')->namespace('Api\V1')->group(function () {
    Route::apiResource('products', 'ProductController');
});

Route::prefix('v2')->namespace('Api\V2')->group(function () {
    Route::apiResource('products', 'ProductController');
});
```

### Proč jste nepoužili GraphQL místo REST API?

REST API jsem zvolil z těchto důvodů:
1. **Jednoduchost** - REST je jednodušší na implementaci a pochopení
2. **Standardizace** - REST je široce používaný a dobře zdokumentovaný přístup
3. **Cachování** - REST lépe využívá HTTP cachování
4. **Potřeby projektu** - Pro tento projekt není potřeba flexibilita, kterou GraphQL nabízí

GraphQL by byl vhodnější pro aplikace s komplexními datovými požadavky, kde klienti potřebují přesně specifikovat, jaká data chtějí získat.

### Jak byste implementovali full-text vyhledávání v aplikaci?

Pro efektivní full-text vyhledávání bych použil:
1. **Laravel Scout** - Pro integraci s vyhledávacími enginy
2. **Elasticsearch nebo Algolia** - Jako vyhledávací engine
3. **Indexování** - Automatické indexování modelů při změnách
4. **Váhování** - Přiřazení různé váhy různým polím (např. kód vs. popis)

```php
// Příklad implementace s Laravel Scout
class Product extends Model
{
    use Searchable;

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'brand_name' => $this->brand->name,
            'material_name' => $this->material->name,
            'price' => $this->price,
        ];
    }
}

// Použití v kontroleru
public function search(Request $request)
{
    return Product::search($request->search)->paginate(10);
}
```

### Jak byste implementovali systém notifikací v aplikaci?

Pro implementaci notifikací bych použil:
1. **Laravel Notifications** - Pro definici a odesílání notifikací
2. **Více kanálů** - Email, databáze, push notifikace
3. **Queueing** - Asynchronní odesílání notifikací pomocí front
4. **Personalizace** - Možnost uživatelů nastavit preferované kanály

```php
// Příklad notifikace
class ProductPriceChanged extends Notification
{
    use Queueable;

    protected $product;
    protected $oldPrice;

    public function __construct(Product $product, $oldPrice)
    {
        $this->product = $product;
        $this->oldPrice = $oldPrice;
    }

    public function via($notifiable)
    {
        return $notifiable->preferredChannels();
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Změna ceny produktu')
            ->line("Cena produktu {$this->product->code} byla změněna.")
            ->line("Stará cena: {$this->oldPrice} Kč")
            ->line("Nová cena: {$this->product->price} Kč");
    }
}
```
