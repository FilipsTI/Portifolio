<?php

/**
 * Seo [ MODEL ]
 * Classe de apoio para o modelo LINK. Pode ser utilizado para gerar SSEO para as paginas do sistema
 * 
 * @copyright (c) 2016, Filipi Pedro Monteiro UPINSIDE TECNOLOGIA
 */
class Seo {

    private $File;
    private $Link;
    private $Data;
    private $Tags;

    /* DADOS POVOADOS */
    private $seoTags;
    private $seoData;

    function __construct($File, $Link) {
        $this->File = strip_tags(trim($File));
        $this->Link = strip_tags(trim($Link));
    }

    public function getTags() {
        $this->checkData();
        return $this->seoTags;
    }

    public function getData() {
        $this->checkData();
        return $this->seoData;
    }

    //PRIVATES
    private function checkData() {
        if (!$this->seoData):
            $this->getSeo();
        endif;
    }

    private function getSeo() {
        $ReadSeo = new Read;

        switch ($this->File):
            //SEO:: POST
            case 'artigo':
                $Admin = (isset($_SERVER['userlogin']['user_level']) && $_SERVER['userlogin']['user_level'] == 3 ? true : false);
                $Check = ($Admin ? '' : 'post_status = 1 AND');

                $ReadSeo->ExeRead("ws_posts", "WHERE {$Check} post_name = :link", "link={$this->Link}");
                if (!$ReadSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($ReadSeo->getResult()[0]);
                    $this->seoData = $ReadSeo->getResult()[0];
                    $this->Data = [$post_title . ' - ' . SITENAME, $post_content, HOME . "/artigo/{$post_name}", HOME . "/uploads/{$post_cover}"];

                    //post:: post_views
                    $ArrUpdate = ['post_views' => $post_views + 1];
                    $update = new Update;
                    $update->ExeUpdate("ws_posts", $ArrUpdate, "WHERE post_id = :postid", "postid={$post_id}");
                endif;
                break;

            //SEO:: CATEGORIA
            case 'categoria':
                $ReadSeo->ExeRead("ws_categories", "WHERE category_name = :link", "link={$this->Link}");
                if (!$ReadSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($ReadSeo->getResult()[0]);
                    $this->seoData = $ReadSeo->getResult()[0];
                    $this->Data = [$category_title . ' - ' . SITENAME, $category_content, HOME . "/categoria/{$category_name}", INCLUDE_PATH . '/images/site.png'];

                    //category:: conta views da categoria
                    $ArrUpdate = ['category_views' => $category_views + 1];
                    $update = new Update;
                    $update->ExeUpdate("ws_categories", $ArrUpdate, "WHERE category_id = :catid", "catid={$category_id}");
                endif;
                break;

            //SEO:: PESQUISA
            case 'pesquisa':
                $ReadSeo->ExeRead("ws_posts", "WHERE post_status = 1 AND (post_title LIKE '%' :link '%' OR post_content LIKE '%' :link '%') ", "link={$this->Link}");
                if (!$ReadSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    $this->seoData['count'] = $ReadSeo->getRowCount();
                    $this->Data = ["Pesquisa por: {$this->Link}" . ' - ' . SITENAME, "Sua pesquisa por {$this->Link} retornou {$this->seoData['count']} resultados", HOME . "/pesquisa/{$this->Link}", INCLUDE_PATH . '/images/site.png'];
                endif;
                break;

            //SEO:: LISTA EMPRESAS
            case 'empresas':
                $Name = ucwords(str_replace("-", " ", $this->Link));
                $this->seoData = ["empresa_link" => $this->Link, "empresa_cat" => $Name];
                $this->Data = ["Empresas {$this->Link} " . SITENAME, "confira o guia completo de sua cidade, e encontre empresas {$this->Link}.", HOME . '/empresas/' . $this->Link, INCLUDE_PATH . '/images/site.png'];
                break;

            //SEO:: EMPRESA SINGLE
            case 'empresa':
                $Admin = (isset($_SERVER['userlogin']['user_level']) && $_SERVER['userlogin']['user_level'] == 3 ? true : false);
                $Check = ($Admin ? '' : 'empresa_status = 1 AND');

                $ReadSeo->ExeRead("app_empresas", "WHERE {$Check} empresa_name = :link", "link={$this->Link}");
                if (!$ReadSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($ReadSeo->getResult()[0]);
                    $this->seoData = $ReadSeo->getResult()[0];
                    $this->Data = [$empresa_title . ' - ' . SITENAME, $empresa_sobre, HOME . "/empresa/{$empresa_name}", HOME . "/uploads/{$empresa_capa}"];

                    //empresa:: post_views da empresa
                    $ArrUpdate = ['empresa_views' => $empresa_views + 1];
                    $update = new Update;
                    $update->ExeUpdate("app_empresas", $ArrUpdate, "WHERE empresa_id = :empresaid", "empresaid={$empresa_id}");
                endif;
                break;

            //SEO:: INDEX
            case 'index':
                $this->Data = [SITENAME . ' - Seu guia de empresas, eventos e baladas', SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
                break;

            //SEO:: 404
            default :
                $this->Data = ['404 Oppsss, nada encontrado', SITEDESC, HOME . '/404', INCLUDE_PATH . '/images/site.png'];
        endswitch;

        if ($this->Data):
            $this->seoTags;
        endif;
    }

    private function setTags() {
        $this->Tags['Title'] = $this->Data[0];
        $this->Tags['Content'] = Check::Words(html_entity_decode($this->Data[1]), 25);
        $this->Tags['Link'] = $this->Data[2];
        $this->Tags['Image'] = $this->Data[3];

        $this->Tags = array_map('strip_tags', $this->Tags);
        $this->Tags = array_map('trim', $this->Tags);

        $this->Data = null;

        //NORMAL PAGE
        $this->seoTags = '<title>' . $this->Tags['Title'] . '</title> ' . "\n";
        $this->seoTags .= '<meta name="description" content="' . $this->Tags['Content'] . '"/>' . "\n";
        $this->seoTags .= '<meta name="robots" content="index, follow"/>' . "\n";
        $this->seoTags .= '<link rel="canonical" href="' . $this->Tags['Link'] . '">' . "\n";
        $this->seoTags .= "\n";

        //FACEBOOK
        $this->seoTags .= '<meta property="og:site_name" content"' . SITENAME . '" />' . "\n";
        $this->seoTags .= '<meta property="og:locale" content"pt-br" />' . "\n";
        $this->seoTags .= '<meta property="og:title" content"' . $this->Tags['Title'] . '" />' . "\n";
        $this->seoTags .= '<meta property="og:description" content"' . $this->Tags['Content'] . '" />' . "\n";
        $this->seoTags .= '<meta property="og:image" content"' . $this->Tags['Image'] . '" />' . "\n";
        $this->seoTags .= '<meta property="og:url" content"' . $this->Tags['Link'] . '" />' . "\n";
        $this->seoTags .= '<meta property="og:type" content"article" />' . "\n";

        //ITEM GROUP (TWITTER)
        $this->seoTags .= '<meta itemprop="name" content"' . $this->Tags['Title'] . '" />' . "\n";
        $this->seoTags .= '<meta itemprop="description" content"' . $this->Tags['Content'] . '" />' . "\n";
        $this->seoTags .= '<meta itemprop="url" content"' . $this->Tags['Link'] . '" />' . "\n";

        $this->Tags = null;
    }

}
