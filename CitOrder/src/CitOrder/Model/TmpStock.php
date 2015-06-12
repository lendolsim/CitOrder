<?php
namespace CitOrder\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TmpStock implements InputFilterAwareInterface
{
    public $id;

    public $caption;    
    public $identifier;    
    public $nb_black_white_print;
    public $nb_color_print;

    ////Import champs 
  	public $serial_number;//Numéro de série de l'équipement
    public $brand;//Marque de l'équipement
    public $model;//Modèle de l'équipement
    public $liste_options;//Libellé et référence des options installées sur l'équipement
    public $exploitation_date;//Date de mise en exploitation de l'équipement
  //  public $site_id;//Numéro généré par Pole Emploi
    public $building;//Bâtiment où se trouve l'équipement
    public $floor;//Etage où se trouve l'équipement
    public $place;//Emplacement de l'équipement
    
    protected $inputFilter;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
         $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->brand = (isset($data['brand'])) ? $data['brand'] : null;
        $this->model = (isset($data['model'])) ? $data['model'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->serial_number = (isset($data['serial_number'])) ? $data['serial_number'] : null;
        $this->nb_black_white_print = (isset($data['nb_black_white_print'])) ? $data['nb_black_white_print'] : null;
        $this->nb_color_print = (isset($data['nb_color_print'])) ? $data['nb_color_print'] : null;
        $this->exploitation_date = (isset($data['exploitation_date'])) ? $data['exploitation_date'] : null;
        $this->building = (isset($data['building'])) ? $data['building'] : null;
        $this->floor = (isset($data['floor'])) ? $data['floor'] : null;
        $this->place = (isset($data['place'])) ? $data['place'] : null;
        $this->liste_options = (isset($data['liste_options'])) ? $data['liste_options'] : null;
      //  $this->site_id = (isset($data['site_id'])) ? $data['site_id'] : null;
        
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }
}
