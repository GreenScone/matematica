<?php

class db
{

    function __construct()
    {
        $config = new Config();
        $i = 0;

        // Profil de configuration de base (0)
        $params[$i]['host'] = $config->db_host;	// Hote de connexion
        $params[$i]['user'] = $config->db_user;	// Identifiant
        $params[$i]['pass'] = $config->db_password;	// Mot de passe
        $params[$i]['base'] = $config->db_name;	// Nom de base de donnees
     /*   $i++;

        // Profil de configuration alternatif (1)
        $params[$i]['host'] = '';	// Hote de connexion
        $params[$i]['user'] = '';	// Identifiant
        $params[$i]['pass'] = '';	// Mot de passe
        $params[$i]['base'] = '';	// Nom de base de donnees
        $i++;

        // Profil de configuration alternatif (2)
        $params[$i]['host'] = '';	// Hote de connexion
        $params[$i]['user'] = '';	// Identifiant
        $params[$i]['pass'] = '';	// Mot de passe
        $params[$i]['base'] = '';	// Nom de base de donnees
        $i++;
*/
        // Des profils de configuration peuvent etre rajoutes

        $this->email = '';	// E-mail de contact
        $this->site = '';	// Nom du site

        $this->params = $params;

        $this->layout = '<br /><font color="#%s"><b>[ ' . $this->lang['library_name'] . ' %s ]</b></font><br />';

    }

    function __destruct()
    {
        $this->Disconnect();
    }

    // Options de configuration
    private $options = array(
        'DEBUG_MODE' => false,
        'IGNORE_ERRORS' => false,
        'FETCH_ARRAY' => MYSQL_BOTH,
        'RETURN_OBJECTS' => true,
        'RETURN_FROM_1' => false
    );

    // Dictionnaire des messages d'erreur et d'information
    private $lang = array(
        'base_error' => 'Impossible de se connecter a la base "<i>%s</i>". ',
        'empty_error' => 'Un parametre "<i>%s</i>" valide est requis. ',
        'error_subject' => 'Erreur MySQL',
        'error_message' => 'Si vous pensez que cette erreur est due a un bug de PHPMySimpleLib, merci de visiter le site www.graphix-webdesign.com afin de le reporter pour qu\'il soit supprime la prochaine version, vous pouvez egalement envoyer cet e-mail accompagne du message d\'erreur ainsi que d\'une �ventuelle explication de ce qui s\'est produit. Si vous savez comment reproduire le probleme ou le corriger, indiquez-le. ',
        'error_report' => 'reporter l\'erreur',
        'host_error' => 'Impossible de se connecter a l\'hote "<i>%s</i>" pour l\'utilisateur <i>%s</i>. ',
        'nbr_array_error' => 'Nombre de valeurs dans le premier tableau different du nombre de valeurs du deuxieme. ',
        'nbr_lines_error' => 'Aucune requete SELECT n\'a encore ete effectuee. ',
        'option_error' => 'Impossible de charger l\'option "<i>%s</i>". ',
        'option_value_error' => 'La valeur "<i>%s</i>" est incorrecte pour l\'option "<i>%s</i>". ',
        'param_error' => 'Le type "<i>%s</i>" ne semble pas etre approprie au parametre "<i>%s</i>". ',
        'profile_error' => 'Impossible de recuperer le profil de configuration "<i>%d</i>", veuillez verifier la configuration de ce profil. ',
        'query_error' => 'La requete suivante a provoque une erreur : "<i>%s</i>". ',
        'library_name' => 'PHPMySimpleLib'
    );

    // FIN DE LA CONFIGURATION

    private $layout;
    private $nbr_lines = -1;
    private $nbr_req = 0;
    private $params;
    private $resource = false;
    private $type_error;

    /**
     * Connexion
     *
     * @param int $profile num�ro du profil de configuration (facultatif)
     *
     * @return bool validite de la connexion
     */
    function Connect($profile = 0)
    {
        if (!is_numeric($profile))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], 'profile'));
        if ($profile >= count($this->params) || $this->params[$profile]['host'] == '' || $this->params[$profile]['user'] == '' || $this->params[$profile]['pass'] == '' || $this->params[$profile]['base'] == '')
            $this->showerror(true, __METHOD__, sprintf($this->lang['profile_error'], $profile));
        elseif (!$this->resource)
        {
            $this->resource = mysql_connect($this->params[$profile]['host'], $this->params[$profile]['user'], $this->params[$profile]['pass'], true)
                or $this->showerror(true, __METHOD__, sprintf($this->lang['host_error'], $this->params[$profile]['host'], $this->params[$profile]['user']));
            @mysql_select_db($this->params[$profile]['base'], $this->resource)
                or $this->showerror(true, __METHOD__, sprintf($this->lang['base_error'], $this->params[$profile]['base']));
            mysql_set_charset('UTF8');
        }
        else
            mysql_ping($this->resource);
        return true;
    }

    /**
     * Deconnexion
     *
     * @return bool validite de la deconnexion
     */
    function Disconnect()
    {
        if ($this->resource)
            return @mysql_close($this->resource);
        return false;
    }

    /**
     * Traitement des requetes SELECT
     *
     * @param mixed $table nom(s) de la/des table(s)
     * @param mixed $field nom(s) du/des champ(s) (facultatif)
     * @param mixed $where clause WHERE (facultatif)
     * @param mixed $group clause GROUP BY (facultatif)
     * @param mixed $order clause ORDER BY (facultatif)
     * @param string $limit clause LIMIT (facultatif)
     *
     * @return array resultat de la requete
     */
    function Select($table, $field = '*', $where = '', $group = '', $order = '', $limit = '')
    {
        if (!$this->is_type('string', $table))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'table'));
        if (!$this->is_type('string', $field))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'field'));
        if (!$this->is_type('string', $where))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'where'));
        if (!$this->is_type('string', $group))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'group'));
        if (!$this->is_type('string', $order))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'order'));
        if (!is_string($limit))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], gettype($limit), 'limit'));
        $table = $this->merge_array($table, ',');
        $field = $this->merge_array($field, ',');
        $where = $this->merge_array($where, ' AND');
        $group = $this->merge_array($group, ',');
        $order = $this->merge_array($order, ',');
        if (empty($field))
            $this->showerror(true, __METHOD__, sprintf($this->lang['empty_error'], 'field'));
        if (!empty($where))
            $where = ' WHERE ' . $where;
        if (!empty($group))
            $group = ' GROUP BY ' . $group;
        if (!empty($order))
            $order = ' ORDER BY ' . $order;
        if (!empty($limit))
            $limit = ' LIMIT ' . $limit;
        $this->Connect();
        $query = 'SELECT ' . $field . ' FROM ' . $table . $where . $group . $order . $limit;
        return $this->ExecuteQuery($query, __METHOD__);
    }

    /**
     * Traitement des requetes INSERT
     *
     * @param mixed $table nom(s) de la/des table(s)
     * @param mixed $field nom(s) du/des champ(s)
     * @param mixed $value valeur(s) du/des champ(s)
     *
     * @return bool validite de l'execution de la requete
     */
    function Insert($table, $field, $value)
    {
        if (!$this->is_type('string', $table))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'table'));
        if (!$this->is_type('string', $field))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'field'));
        if (!$this->is_type(array('string', 'integer'), $value))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'value'));
        if (count($field) != count($value))
            $this->showerror(true, __METHOD__, $this->lang['nbr_array_error']);
        $field_tmp = $value_tmp = array();
        for ($i = 0; $i < count($field); $i++)
            if (!empty($field[$i]))
            {
                array_push($field_tmp, $field[$i]);
                array_push($value_tmp, $value[$i]);
            }
        $field = $field_tmp;
        $value = $value_tmp;
        $table = $this->merge_array($table, ',');
        $field = $this->merge_array($field, ',');
        $value = $this->merge_array($value, ',', true);
        if (empty($field))
            $this->showerror(true, __METHOD__, sprintf($this->lang['empty_error'], 'field'));
        $query = 'INSERT INTO ' . $table . ' (' . $field . ') VALUES (' . $value . ')';
        return $this->ExecuteQuery($query, __METHOD__);
    }

    /**
     * Traitement des requetes UPDATE
     *
     * @param mixed $table nom(s) de la/des table(s)
     * @param mixed $field nom(s) du/des champ(s)
     * @param mixed $value valeur(s) du/des champ(s)
     * @param mixed $where clause WHERE (facultatif)
     *
     * @return bool validite de l'execution de la requete
     */
    function Update($table, $field, $value, $where = '')
    {
        if (!$this->is_type('string', $table))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'table'));
        if (!$this->is_type('string', $field))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'field'));
        if (!is_numeric($value) && !$this->is_type(array('string', 'integer'), $value))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'value'));
        if (!$this->is_type('string', $where))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'where'));
        if (count($field) != count($value))
            $this->showerror(true, __METHOD__, $this->lang['nbr_array_error']);
        if (is_array($field) && is_array($value))
        {
            $fieldvalue = '';
            for ($i = 0; $i < count($field); $i++)
                if (!empty($field[$i]))
                    $fieldvalue .= ((is_string($value[$i]) || $value[$i] != 0) ? $field[$i] . ' = "' . mysql_real_escape_string($value[$i]) . '"' : $field[$i]) . ', ';
            if (strlen($fieldvalue) > 0)
                $fieldvalue = substr($fieldvalue, 0, - 2);
            else
                $this->showerror(true, __METHOD__, sprintf($this->lang['empty_error'], 'field'));
        }
        else
            $fieldvalue = (is_string($value) || $value != 0) ? $field . ' = "' . mysql_real_escape_string($value) . '"' : $field;
        $where = $this->merge_array($where, ' AND');
        if (!empty($where))
            $where = ' WHERE ' . $where;
        $query = 'UPDATE ' . $table . ' SET ' . $fieldvalue . $where;
        return $this->ExecuteQuery($query, __METHOD__);
    }

    /**
     * Traitement des requetes DELETE
     *
     * @param string $table nom de la table
     * @param mixed $where clause WHERE (facultatif)
     * @param mixed $order clause ORDER BY (facultatif)
     * @param string $limit clause LIMIT (facultatif)
     *
     * @return bool validite de l'execution de la requete
     */
    function Delete($table, $where = '', $order = '', $limit = '')
    {
        if (!is_string($table))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], gettype($table), 'table'));
        if (!$this->is_type('string', $where))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'where'));
        if (!$this->is_type('string', $order))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'order'));
        if (!is_string($limit))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], gettype($limit), 'limit'));
        $where = $this->merge_array($where, ' AND');
        $order = $this->merge_array($order, ',');
        if (!empty($where))
            $where = ' WHERE ' . $where;
        if (!empty($order))
            $order = ' ORDER BY ' . $order;
        if (!empty($limit))
            $limit = ' LIMIT ' . $limit;
        $query = 'DELETE FROM ' . $table . $where . $order . $limit;
        return $this->ExecuteQuery($query, __METHOD__);
    }

    /**
     * Execution d'une requete libre
     *
     * @param string $query requete a executer
     *
     * @return mixed validite de l'execution ou resultat de la requete
     */
    function ExecuteQuery($query, $function = __METHOD__)
    {
        if (!is_string($query))
            $this->showerror(true, $function, sprintf($this->lang['param_error'], gettype($query), 'query'));
        if (empty($query))
            $this->showerror(true, $function, sprintf($this->lang['empty_error'], 'query'));
        $this->Connect();
        $this->nbr_req++;
        if (!($result = mysql_query($query, $this->resource)) && !$this->options['IGNORE_ERRORS'])
        {
            $this->showerror(false, $function, sprintf($this->lang['query_error'], htmlspecialchars($query)), mysql_error());
            return false;
        }
        elseif ($this->options['DEBUG_MODE'])
            $this->showdebug($function, $query);
        if (substr($query, 0, 6) == 'SELECT')
        {
            $i = 0;
            if ($this->options['RETURN_FROM_1'])
                $i++;
            $return = '';
            while ((!$this->options['RETURN_OBJECTS'] && $select = mysql_fetch_array($result, $this->options['FETCH_ARRAY'])) || $select = mysql_fetch_object($result))
                $return[$i++] = $select;
            if ($this->options['RETURN_FROM_1'])
                $return[0] = $i - 1;
            $this->nbr_lines = $i;
            return $return;
        }
        else
            return mysql_insert_id($this->resource);
    }

    /**
     * Activation des options
     *
     * @param mixed $name nom de l'option
     * @param mixed $value activer/desactiver l'option (facultatif)
     *
     * @return bool validite de la selection de l'option
     */
    function SetOption($name, $value = true)
    {
        if (!$this->is_type('string', $name))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'name', __METHOD__));
        if (!$this->is_type(array('boolean', 'string'), $value))
            $this->showerror(true, __METHOD__, sprintf($this->lang['param_error'], $this->type_error, 'value', __METHOD__));
        if (count($name) != count($value) && is_array($value))
            $this->showerror(true, __METHOD__, $this->lang['nbr_array_error']);
        $error = false;
        if (!is_array($name))
            $name = array($name);
        if (!is_array($value))
        {
            for ($i = 0; $i < count($name); $i++)
                $value_tmp[$i] = $value;
            $value = $value_tmp;
        }
        for ($i = 0; $i < count($name); $i++)
        {
            $name[$i] = strtoupper($name[$i]);
            if (!array_key_exists($name[$i], $this->options))
            {
                if (!$this->options['IGNORE_ERRORS'])
                    $this->showerror(false, __METHOD__, sprintf($this->lang['option_error'], $name[$i]));
                $error = true;
            }
            elseif ($name[$i] == 'FETCH_ARRAY')
            {
                if (!is_string($value[$i]))
                {
                    if (!$this->options['IGNORE_ERRORS'])
                        $this->showerror(false, __METHOD__, sprintf($this->lang['option_value_error'], $value[$i], $name[$i]));
                    $error = true;
                }
                switch ($value[$i])
                {
                    case 'BOTH':
                        $this->options[$name[$i]] = MYSQL_BOTH;
                        break;
                    case 'ASSOC':
                        $this->options[$name[$i]] = MYSQL_ASSOC;
                        break;
                    case 'NUM':
                        $this->options[$name[$i]] = MYSQL_NUM;
                        break;
                    default:
                        if (!$this->options['IGNORE_ERRORS'])
                            $this->showerror(false, __METHOD__, sprintf($this->lang['option_value_error'], $value[$i], $name[$i]));
                        $error = true;
                }
            }
            else
            {
                if (is_bool($value[$i]))
                    $this->options[$name[$i]] = $value[$i];
                else
                {
                    if (!$this->options['IGNORE_ERRORS'])
                        $this->showerror(false, __METHOD__, sprintf($this->lang['option_value_error'], $value[$i], $name[$i]));
                    $error = true;
                }
            }
        }
        return !$error;
    }

    /**
     * Nombre de requetes effectuees
     *
     * @return int nombre de requetes effectuees avec la session courante
     */
    function GetNbrReq()
    {
        return $this->nbr_req;
    }

    /**
     * Nombre de lignes retournees lors de la derni?re requete SELECT
     *
     * @return int nombre de lignes retournees lors de la derni?re requete SELECT
     */
    function GetNbrLines()
    {
        if ($this->nbr_lines == -1)
            $this->showerror(false, __FUNCTION__, $this->lang['nbr_lines_error']);
        return $this->nbr_lines;
    }

    /**
     * Affichage du resultat du debug mode (fonction privee)
     *
     * @param string $function nom de la fonction concernee
     * @param string $query requ?te concernee
     */
    function showdebug($function, $query)
    {
        printf($this->layout, '008000', '| ' . __CLASS__ . '::' . $function . '() : ' . htmlspecialchars($query));
    }

    /**
     * Debug mode automatique en cas d'erreur (fonction privee)
     *
     * @param boolean $fatal gravite de l'erreur
     * @param string $function nom de la fonction concernee
     * @param string $message message d'erreur
     * @param string $error message d'erreur interne (facultatif)
     */
    function showerror($fatal, $function, $message, $error = '')
    {
        if (!empty($error))
            $error = '(' . $error . ') ';
        $content = sprintf($this->layout, 'ff0000', '| ' . __CLASS__ . '::' . $function . '() : ' . $message . $error . '| <a href="mailto:' . $this->email . '?subject=' . $this->lang['error_subject'] . ' ' . $this->site . '&body=' . $this->lang['error_message'] . '">' . $this->lang['error_report'] . '</a> | Page : ' . $_SERVER['PHP_SELF'] . ' | ' . (!empty($_SERVER['QUERY_STRING']) ? 'Query string : ' . $_SERVER['QUERY_STRING'] . ' | ' : '') . 'Time : ' . date('d/m/Y H:i:s'));
        if ($fatal)
            die($content);
        echo $content;
    }

    /**
     * Verification du type des elements du tableau
     *
     * @param mixed $type type de l'element
     * @param mixed $array tableau d'elements
     *
     * @return bool validite du type des elements du tableau
     */
    function is_type($type, $array)
    {
        if (!is_array($array))
            $array = array($array);
        if (!is_array($type))
            $type = array($type);
        foreach ($array as $array_value)
        {
            $return = false;
            foreach ($type as $type_value)
            {
                if (gettype($array_value) == $type_value)
                {
                    $return = true;
                    break;
                }
            }
            if (!$return)
            {
                $this->type_error = gettype($array_value);
                return false;
            }
        }
        return true;
    }
    function merge_array($array, $delimiter, $quote = false)
    {
        $conn = $this->Connect();
        if (is_array($array))
        {
            $array_tmp = $array;
            unset($array);
            $array = "";
            foreach ($array_tmp as $array_tmp_value)
                if (!empty($array_tmp_value) || $quote)
                    $array .= ($quote ? '"' . mysql_real_escape_string($array_tmp_value) . '"' : $array_tmp_value) . $delimiter . ' ';
            $array = substr($array, 0, - strlen($delimiter) - 1);
        }
        elseif ($quote)
            $array = '"' . mysql_real_escape_string($array) . '"';
        return $array;
    }
}



/**
 * Fusion des elements d'un tableau (fonction privee)
 *
 * @param mixed $array tableau d'elements
 * @param string $delimiter delimiteur
 * @param boolean $quote ajouter des guillemets (facultatif)
 *
 * @return string fusion des elements du tableau
 */


?>
