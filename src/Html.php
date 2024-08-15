<?php

namespace RodrigoAleixo\Curl;
use DOMDocument;
class Html
{
    public $htmlFull;
    public $htmlPresent;
    public $links;
    public $tagsArray;
    public $tagSelected;
    public $classes;

    public function setTag($tag) {
        $this->tagSelected = $tag;
    }
    public function setHtml($html) {
        $this->htmlPresent = $html;
    }
    public function viewTagSelected(){
        return $this->tagsArray->{$this->tagSelected};
    }

    public function getLinks()
    {
        $pattern = '/<a\s+(?:[^>]*?\s+)?href="([^"]*)"/';
        if (preg_match_all($pattern, $this->htmlPresent, $matches)) {
            return $matches[1];
        } else {
            return false;
        }
    }
    public function getElementByTag() {
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $this->htmlPresent);

        $elementosPorTag = array();

        // Percorra todas as tags do documento
        $tags = $doc->getElementsByTagName('*');
        foreach ($tags as $tag) {
            $nomeDaTag = $tag->tagName;
            // Adicione a tag ao array associativo usando o nome da tag como chave
            $elementosPorTag[$nomeDaTag][] = $doc->saveHTML($tag);
        }
        return (object) $elementosPorTag;
    }

    public function getElementById() {

        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $this->htmlPresent);

        $elementosPorId = array();

        // Percorra todas as tags que têm um atributo "id"
        $tagsComId = $doc->getElementsByTagName('*');
        foreach ($tagsComId as $tag) {
            $id = $tag->getAttribute('id');

            // Verifique se a tag tem um atributo "id"
            if (!empty($id)) {
                // Use o ID como chave no array associativo
                $elementosPorId[$id][] = $doc->saveHTML($tag);
            }
        }

        return (object) $elementosPorId;
    }

    public function getElementByClass($tag = '*') {
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $this->htmlPresent);

        $elementosPorClasse = array();

        // Percorra todas as tags <div>
        $divs = $doc->getElementsByTagName($tag);
        foreach ($divs as $div) {
            // Obtenha as classes da tag <div>
            $classes = $div->getAttribute('class');

            // Verifique se a tag <div> tem classes
            if (!empty($classes)) {
                $classes = explode(' ', $classes);

                // Percorra todas as classes
                foreach ($classes as $classe) {
                    // Adicione a tag <div> ao array associativo usando a classe como chave
                    $elementosPorClasse[$classe][] = $doc->saveHTML($div);
                }
            }
        }

        return $elementosPorClasse;
    }
    public function getElementByIdParcial($substring) {
        $html = $this->htmlPresent;
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        $elementos = $doc->getElementsByTagName('*');
        $resultado = array();
        foreach ($elementos as $elemento) {
            $id = $elemento->getAttribute('id');
            if ($id !== '') {
                if (strpos($id, $substring) !== false) {
                    $resultado[] = $doc->saveHTML($elemento);
                }
            }
        }
        return $resultado;
    }

    public function getAttributesFirstTag() {
        $html = $this->htmlPresent;

        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $this->htmlPresent);

        $elementos = $doc->getElementsByTagName('*');
        foreach ($elementos as $elemento) {
            // Encontre a primeira tag e retorne seus atributos.
            $atributos = array();

            foreach ($elemento->attributes as $atributo) {
                $atributos[$atributo->name] = $atributo->value;
            }
            if (!empty($atributos)) {
                $this->tagAttributes = $atributos;
                return $atributos;
            }
        }
        return null; // Retorna null se nenhuma tag.
    }
    public function getElementByNivel($nivel = 0) {
        $html = $this->htmlPresent;
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$html);

        $arrayMultidimensional = array();

        // Encontre o elemento raiz que não é <html> ou <body>
        $raiz = $doc->documentElement;
        while ($raiz->tagName === 'html' || $raiz->tagName === 'body') {
            $raiz = $raiz->firstChild;
        }

        // Chame o método da classe interna para construir a array
        $this->construir($raiz, $arrayMultidimensional, 0, $nivel);

        return $arrayMultidimensional;
    }
    public function construir($elemento, &$array, $nivelAtual, $nivelDesejado) {
        if ($elemento->nodeType === XML_ELEMENT_NODE) {
            if ($nivelAtual === $nivelDesejado) {
                // Adicione o conteúdo HTML da tag atual
                $array[$elemento->tagName][] = $elemento->ownerDocument->saveHTML($elemento);
            } else {
                foreach ($elemento->childNodes as $filho) {
                    self::construir($filho, $array, $nivelAtual + 1, $nivelDesejado);
                }
            }
        }
    }
}
