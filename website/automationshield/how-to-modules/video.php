<form class="how-to-tab-button-wrapper-online" >
    <label class="how-to-tab-button-online" data-txt="txtHowToSVKvid" >
        <input type="radio" name="how-to-tab" value="SVKvid" id="how-to-SVKvid" checked hidden>
        Slovenský návod + UKR titulky
    </label>
    <div class="how-to-tab-button-divider"></div>
    <label class="how-to-tab-button-online" data-txt="txtHowToENGvid">
        <input type="radio" name="how-to-tab" value="ENGvid" id="how-to-ENGvid" hidden>
        Anglický návod + UKR titulky
    </label>
</form>

<!--********************************** SVK vid *******************************-->
      <div class="how-to-content-wrapper" id="how-to-SVKvid-wrapper" show="true">
        <div class="how-to-top-line">
          <button id="share-SVK" class="share-button" clicked="false" videobutton>
            <?php include 'sharesvg.php' ?>
            <p data-txt="txtShare"> </p>
            <div class="share-copied" data-txt="txtCopied" show="false"> </div>
          </button>
        </div>
        <div style="position: relative; margin: 10px auto auto auto; width: 90%; padding-bottom: 51%; /* 16:9 pomer */ height: 0; overflow: hidden;">
            <iframe
                src="https://www.youtube.com/embed/om-5N2jFusI?si=czEwHR9kb09TE-ld"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
            </iframe>
        </div>
        
      </div>
<!--********************************** ENG vid *******************************-->
      <div class="how-to-content-wrapper" id="how-to-ENGvid-wrapper" show="false">
        <div class="how-to-top-line">
          <button id="share-ENG" class="share-button" clicked="false" videobutton>
            <?php include 'sharesvg.php' ?>
            <p data-txt="txtShare"> </p>
            <div class="share-copied" data-txt="txtCopied" show="false" videobutton> </div>
          </button>
        </div>
        <div style="position: relative; margin: 10px auto auto auto; width: 90%; padding-bottom: 51%; /* 16:9 pomer */ height: 0; overflow: hidden;">
            <iframe
                src="https://www.youtube.com/embed/hNNOyAlImQ4?si=tt4-l34vD0sAWH43"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
            </iframe>
        </div>
        
      </div>
      
      </div>