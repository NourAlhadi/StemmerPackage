# PHP ISRI Stemmer

The first free and open source Arabic stemmer ever written in PHP for Laravel framework implementing ISRI algorithm.

Due to lack of support for Arabic language despite its wonderful features and grammar, and as an Arabian open source tech geek in love with technologies, I felt like it's my duty to help my native language to rise, and help making Arab programmers life a bit easier.

This package contains the ISRI Stemming class build from scratch by myself and willing to make this project for global PHP use (not restricted to Laravel) next step will be Symfony :heart_eyes: .

Please note that this is just the beginning, and I'm willing to continue building a project that will serve the whole Arab language.

### Install

You can install this package using composer: 

    composer require nouralhadi/stemmer
   
For Laravel < 5.5, You should add the package to your service providers array in `config/app.php`:

    \Nouralhadi\Stemmer\StemmerServiceProvider::class,

### How to use

You can visit this route `/stemmer` after installing this package to access the testing page of the package.

The only feature it contains (for now) is the `stem` feature, which accepts an arabic word and return its root.

You can use the Stemmer class by Injecting it inside your caller function / controller:

    use Nouralhadi\Stemmer\Http\Helpers\ISRIStemmer;
    public function test(ISRIStemmer $stemmer){
        $string = 'وزراء';
        echo $stemmer->stem($string);
        // Resulting: وزر
    } 
    

Or you can stem a complete sentence by splitting into words and steam each:

    use Nouralhadi\Stemmer\Http\Helpers\ISRIStemmer;
    public function test(ISRIStemmer $stemmer){
        $string = 'كتب المستخدم رسالة إلى مدير الموقع';
        $words = mb_split(' ',$string);
        
        $ret = [];
        foreach ($words as $word){
            array_push($ret, $this->Stemmer->stem($word));
        }
        $result = implode(' ', $ret);
        // Resulting: كتب خدم رسل ألى دير وقع 
    }

### Contribute

Anyone willing to contribute to this project is welcomed, any help of any kind will be appreciated, and if you're ready to help then please reach out to my personal email `nouralhadi99@gmail.com`. 

### License

This Project is an open-sourced software licensed under the [GPLv3 License](https://opensource.org/licenses/GPL-3.0).
