<div class="content home">

    <aside>
        <h1 class="boxtitle">Estatísticas de Acesso:</h1>

        <article class="sitecontent boxaside">
            <h1 class="boxsubtitle">Conteúdo:</h1>

            <?php
            $read = new Read;

            $read->FullRead("SELECT SUM(siteviews_views) AS views FROM ws_siteviews");
            $Views = $read->getResult()[0]['views'];

            $read->FullRead("SELEC SUM(siteviews_users) AS users FROM ws_siteviews");
            $Users = $read->getResult()[0]['users'];

            $read->FullRead("SELECT SUM(siteviews_pages) AS  pages FROM ws_siteviews");
            $ResPages = $read->getResult()[0]['pages'];
            $Pages = substr($ResPages / $Users, 0, 5);

            $read->ExeRead("ws_posts");
            $Posts = $read->getRowCount();

            $read->ExeRead("app_empresas");
            $Empresas = $read->getRowCount();
            ?>

            <ul>
                <li class="view"><span><?= $Views; ?></span> visitas</li>
                <li class="user"><span><?= $Users; ?></span> usuários</li>
                <li class="page"><span><?= $Pages; ?></span> pageviews</li>
                <li class="line"></li>
                <li class="post"><span><?= $Posts; ?></span> posts</li>
                <li class="emp"><span><?= $Empresas; ?></span> empresas</li>
                <!--<li class="comm"><span><?= $Views; ?></span> comentários</li>-->
            </ul>
            <div class="clear"></div>
        </article>

        <article class="useragent boxaside">
            <h1 class="boxsubtitle">Navegador:</h1>

            <?php
            //LE O TOTAL DE VISITAS DOS NAVEGADORES
            $read->FullRead("SELECT SUM(agent_views) AS TotalViews FROM ws_siteviews_agent");
            $TotalViews = $read->getResult()[0]['TotalViews'];

            $read->ExeRead("ws_siteviews_agent", "ORDER BY agent_views DESC LIMIT 3");
            if (!$read->getResult()):
                WSErro("Oppsss, Ainda não existem estatísticas de navegadores", WS_INFOR);
            else:
                echo "<ul>";
                foreach ($read->getResult() as $nav):
                    extract($nav);
                
                    //REALIZA PORCENTAGEM DE VISITAS POR NAVEGADOR
                    $percent = substr( ( $agent_views / $TotalViews ) * 100, 0, 5);
                    ?>
                    <li>
                        <p><strong><?= $agent_name; ?>:</strong> <?= $percent; ?>%</p>
                        <span style="width: <?= $percent; ?>%"></span>
                        <p> <?=$agent_views;?> visitas</p>
                    </li>
                    <?php
                endforeach;
                echo "</ul>";
            endif;
            ?>

            <div class="clear"></div>
        </article>
    </aside>

    <section class="content_statistics">

        <h1 class="boxtitle">Publicações:</h1>

        <section>
            <h1 class="boxsubtitle">Artigos Recentes:</h1>

            <?php for ($i = 1; $i <= 3; $i++): ?>
                <article<?php if ($i % 2 == 0) echo ' class="right"'; ?>>

                    <div class="img thumb_small"></div>
                    <h1><a target="_blank" href="../artigo/uri-do-artigo" title="Ver Post">Tchau iPhone: Galaxy S3 supera o 4S e é o celular mais vendido do mundo</a></h1>
                    <ul class="info post_actions">
                        <li><strong>Data:</strong> <?= date('d/m/Y H:i'); ?>Hs</li>
                        <li><a class="act_view" target="_blank" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Ver no site">Ver no site</a></li>
                        <li><a class="act_edit" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Editar">Editar</a></li>
                        <!--<li><a class="act_inative" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Ativar">Ativar</a></li>-->
                        <li><a class="act_ative" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Inativar">Ativar</a></li>
                        <li><a class="act_delete" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Excluir">Deletar</a></li>
                    </ul>

                </article>
            <?php endfor; ?>
        </section>          


        <section>
            <h1 class="boxsubtitle">Artigos Mais Vistos:</h1>

            <?php for ($i = 1; $i <= 3; $i++): ?>
                <article<?php if ($i % 2 == 0) echo ' class="right"'; ?>>

                    <div class="img thumb_small"></div>
                    <h1><a target="_blank" href="../artigo/uri-do-artigo" title="Ver Post">Tchau iPhone: Galaxy S3 supera o 4S e é o celular mais vendido do mundo</a></h1>
                    <ul class="info post_actions">
                        <li><strong>Data:</strong> <?= date('d/m/Y H:i'); ?>Hs</li>
                        <li><a class="act_view" target="_blank" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Ver no site">Ver no site</a></li>
                        <li><a class="act_edit" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Editar">Editar</a></li>
                        <!--<li><a class="act_inative" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Ativar">Ativar</a></li>-->
                        <li><a class="act_ative" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Inativar">Ativar</a></li>
                        <li><a class="act_delete" href="painel.php?exe=posts/post&id=ID_DO_POST" title="Excluir">Deletar</a></li>
                    </ul>

                </article>
            <?php endfor; ?>
        </section>                           

    </section> <!-- Estatísticas -->

    <div class="clear"></div>
</div> <!-- content home -->