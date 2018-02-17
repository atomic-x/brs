<?php
class GoogleWeatherData  
{  
    private $GOOGLE_WEATHER_URL = "http://www.google.com/ig/api?weather=%zip%&hl=en&referrer=googlecalendar";
    private $condition;
    private $temperature;
    private $wind;
    private $icon;
    private $city;
    
    // A helper method to get weather details by zip code.
    public function __construct($zip) {
        $url = str_replace("%zip", $zip, $this->GOOGLE_WEATHER_URL);
        $weatherXML = simplexml_load_file($url);
        $city = $weatherXML->weather->forecast_information->city["data"];
        $current_conditions = $weatherXML->weather->current_conditions;
        $this->condition = $current_conditions->condition["data"];
        $this->temperature = $current_conditions->temp_f["data"];
        //$this->wind = formatDirection($current_conditions->wind_condition["data"]);
        $this->icon = $current_conditions->icon["data"];
        $this->city = $city;
        return $current_weather;
    }
    
    //magic function for setting variables  
    public function __set($var, $val)  
    {  
        $this->$var = $val;  
    }  
    
    //magic function for returning variables  
    public function __get($var)
    {
        return $this->$var;
    }
    
    // A helper method to format directional abbreviations.
    public function formatDirection($wind) {
        $abbreviated = array(" N ", " S ", " E ", " W ", " NE ", " SE ", " SW ", " NW ");
        $full_name = array(" North ", " South ", " East ", " West ", " North East ", " South East ", " South West ", " North West ");
        return str_replace($abbreviated, $full_name, str_replace("mph", "miles per hour", $wind));
    }
}
?>