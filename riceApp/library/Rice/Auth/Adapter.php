<?php
/**
 * Adaptador de autorizacion para la aplicacion
 */
class Rice_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    const NOT_FOUND_MESSAGE = "Cuenta no encontrada";
    const BAD_PW_MESSAGE = "Contraseña inválida";
    /**
     *
     * @var Usuario
     */
    protected $usuario;


    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }
    
    
    public function authenticate()
    {
        try
        {
            
        }
        catch (Exception $e) 
        {
        
        }
        return $this->result(Zend_Auth_Result::SUCCESS);
    }

    /**
     * Factory for Zend_Auth_Result
     *
     *@param integer    The Result code, see Zend_Auth_Result
     *@param mixed      The Message, can be a string or array
     *@return Zend_Auth_Result
     */
    public function result($code, $messages = array()) {
    	
        if (!is_array($messages)) {
            $messages = array($messages);
        }
        return new Zend_Auth_Result(
            $code,
            $this->usuario,
            $messages
        );
    }
}

