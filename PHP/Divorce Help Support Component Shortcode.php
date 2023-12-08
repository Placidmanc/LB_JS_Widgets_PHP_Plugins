function divorce_help_shortcode()
{
$output = '<style>
  .help-wrapper {
    position: relative;
    display: flex;
    justify-content: flex-start;
  }

  .help-lh {
    display: flex;
    padding-left: 0;
    margin-left: 0;
  }

  .help-rh {
    display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
  }

  .help-btn {
    display: flex;
    flex-direction: row;
    align-items: center;
    background-color: white;
    color: #CC4877;
    padding: 11px;
    width: 100%;
    border-radius: 36px 36px 36px 36px;
    box-shadow: 0px 0px 16px 0px rgba(0, 0, 0, 0.15);
    gap: 26px;
    cursor: pointer;
    margin-bottom: 26px;
    max-height: 60px;
  }

  .help-block {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: 14px;
  }

  .help-img-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 111px;
    height: auto;
    padding: 0;
    box-shadow: none;
    border-radius: 0;
  }

  .help-btn img {
    width: 39px;
    height: auto;
  }

  .help-btn img:nth-child(2) {
    display: none;
  }

  .help-btn:hover img:nth-child(1) {
    display: none;
  }

  .help-btn:hover img:nth-child(2) {
    display: block;
  }

  .help-btn p {
    font-size: 20px;
    line-height: 0px;
    font-weight: 900;
    margin-bottom: 0px !important;
  }

  .help-btn:hover {
    background-color: #CC4877;
    color: white;
  }

  .helpBtnActive {
    background-color: #CC4877 !important;
    color: white !important;
  }

  .helpBtnActive img:nth-child(1) {
    display: none;
  }

  .helpBtnActive img:nth-child(2) {
    display: block;
  }

  .helpinfo-box {
    display: flex;
    flex-direction: column;
    padding: 0;
    margin: 0;
  }

  .helpinfo-box>h3 {
    font-size: 40px;
    font-weight: 900;
    color: #CC4877;
    margin-top: 0;
    margin-bottom: 35px;
  }

  .help-block {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 56px;
  }

  .help-block img {
    max-width: 111px;
  }

  .help-block-inner {
    display: flex;
    flex-direction: column;
  }

  .help-block-inner h3 {
    font-size: 18px;
    font-weight: 900;
    line-height: 18px;
    color: #56B2B6;
    margin-bottom: 0;
  }

  .help-block-inner p {
    font-size: 18px;
    font-weight: 500;
    line-height: 18px;
    color: #808080;
    max-width: 430px;
  }

  @media only screen and (max-width: 767px) {
    .help-wrapper {
      flex-direction: column;
      padding: 0 5%;
      width: 100%;
    }

    .help-lh {
      width: 100%;
      flex-direction: column;
      align-items: center;
      margin-right: 0;
    }

    .help-rh {
      margin-top: 0;
      width: 100%;
    }

    .helpinfo-box>h3 {
      font-size: 24px;
    }

    .help-block {
      flex-direction: column;
      align-items: flex-start;
      gap: 14px;
    }

    .help-img-wrapper {
      width: 70px;
      height: 70px;
      padding: 10px;
      background-color: white;
      box-shadow: 0px 0px 16px 0px rgba(0, 0, 0, 0.15);
      border-radius: 17px;
    }

    .help-img-wrapper img {
      width: 70px;
    }
  }

  @media only screen and (min-width: 767px) {
    .help-wrapper {
      flex-direction: column;
      box-sizing: border-box;
      padding: 0 5%;
      width: 97%;
    }

    .help-lh {
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: space-between;
      width: 100%;
      margin-right: 0;
    }

    .help-rh {
      margin-top: 50px;
      width: 100%;
    }

    .help-btn {
      width: 310px;
    }

    .helpinfo-box>h3 {
      font-size: 30px;
    }

    .help-block {
      flex-direction: column;
      align-items: flex-start;
      gap: 14px;
    }

    .help-block-inner p {
      max-width: 90%;
    }

    .help-img-wrapper {
      width: 70px;
      height: 70px;
      padding: 10px;
      background-color: white;
      box-shadow: 0px 0px 16px 0px rgba(0, 0, 0, 0.15);
      border-radius: 17px;
    }

    .help-img-wrapper img {
      width: 70px;
    }

  }

  @media only screen and (min-width: 1000px) {
    .help-wrapper {
      flex-direction: row;
      padding: 0;
      width: 100%;
      max-width: 1200px;
    }

    .help-lh {
      width: 40%;
      min-width: 500px;
      padding-right: 100px;
      flex-direction: column;
      flex-wrap: nowrap;
      justify-content: flex-start;
    }

    .help-rh {
      margin-top: 0;
      width: 100%;
      min-width: 500px;
    }

    .help-btn {
      width: 323px;
    }

    .helpinfo-box>h3 {
      font-size: 40px;
    }

    .help-block {
      flex-direction: row;
    }

    .help-img-wrapper {
      width: 70px;
      height: auto;
      box-shadow: none;
      margin-right: 30px;
    }

    .help-img-wrapper img {
      width: 70px;
      max-width: 70px;
    }

    .help-block-inner p {
      max-width: 80%;
    }

  }
</style>

<div class="help-wrapper">
  <div class="help-lh">
    <div class="help-btn" id="helpbtn1" onclick="toggleSection(`help1`)">
      <img src="/wp-content/uploads/2023/05/plus-btn.png" />
      <img src="/wp-content/uploads/2023/05/minus-btn.png" />
      <p>FOR YOU IN PERSON</p>
    </div>
    <div class="help-btn" id="helpbtn2" onclick="toggleSection(`help2`)">
      <img src="/wp-content/uploads/2023/05/plus-btn.png" />
      <img src="/wp-content/uploads/2023/05/minus-btn.png" />
      <p>FOR YOU ONLINE</p>
    </div>
    <div class="help-btn" id="helpbtn3" onclick="toggleSection(`help3`)">
      <img src="/wp-content/uploads/2023/05/plus-btn.png" />
      <img src="/wp-content/uploads/2023/05/minus-btn.png" />
      <p>FOR COPARENTING</p>
    </div>
    <div class="help-btn" id="helpbtn4" onclick="toggleSection(`help4`)">
      <img src="/wp-content/uploads/2023/05/plus-btn.png" />
      <img src="/wp-content/uploads/2023/05/minus-btn.png" />
      <p>FOR CHILDREN</p>
    </div>
  </div>
  <div class="help-rh">
    <div id="help1info">
      <div class="helpinfo-box">
        <h3>FOR YOU <span style="font-weight: 300;">IN PERSON</span></h3>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/DivorceClub-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>THE DIVORCE CLUB BRISTOL</h3>
            <p>If you are going through a divorce, thinking about a divorce, are newly separated, or already divorced,
              then this group is for you. Divorce can be challenging, but it is also a real opportunity for personal
              growth and change.</p>
          </div>
        </div>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/relate-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>AVON RELATE</h3>
            <p>Relate Avon is a member of the Relate Federation and has provided services in the areas of Bath and North
              East Somerset, Bristol, North Somerset and South Gloucestershire for over 60 years.
              www.relate-avon.org.uk</p>
          </div>
        </div>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/NetworkCounselling-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>NETWORK COUNSELLING</h3>
            <p>Network Counselling is staffed by professional counsellors who volunteer their time to provide an
              affordable counselling service to the people of Bristol and surrounding areas. No one is turned away
              through lack of ability to pay. They offer a welcoming, safe and confidential space where people can be
              listened to, accepted and understood; where they can be supported in exploring the problems they face and
              finding their own way forward.</p>
          </div>
        </div>
      </div>
    </div>

    <div id="help2info" style="display:none;">
      <div class="helpinfo-box">
        <h3>FOR YOU <span style="font-weight: 300;">ONLINE</span></h3>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/Only-Mums-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>ONLY MUMS</h3>
            <p>Only Mums work in partnership with a wide range of national professionals to give you up to date,
              practical information on divorce and separation.</p>
          </div>
        </div>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/only-dads-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>ONLY DADS</h3>
            <p>Only Dads work in partnership with a wide range of national professionals to give you up to date,
              practical information on divorce and separation.</p>
          </div>
        </div>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/netmums-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>NETMUMS</h3>
            <p>Net Mums is a great hub of knowledge and support for single parents, especially those with disabled
              children that are now coping as a single parent.</p>
          </div>
        </div>
      </div>
    </div>

    <div id="help3info" style="display:none;">
      <div class="helpinfo-box">
        <h3>FOR <span style="font-weight: 300;">COPARENTING</span></h3>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/Our-Family-Wizard-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>OUR FAMILY WIZARD</h3>
            <p>Your schedule, files, contacts, and communication are contained within one app, so you can solve shared
              custody challenges faster and without confusion. That means less conflict and more energy to focus on your
              children.</p>
          </div>
        </div>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/2houses-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>2 HOUSES</h3>
            <p>2 Houses helps separated parents communicate and become better organised for the wellbeing of their
              children.</p>
          </div>
        </div>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/cozi-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>COZI</h3>
            <p>Cozi makes it easy to stay on top of it all. Track everyone’s activities, colour code, manage school
              events, family agendas, grocery lists and more. </p>
          </div>
        </div>
      </div>
    </div>

    <div id="help4info" style="display:none;">
      <div class="helpinfo-box">
        <h3>FOR <span style="font-weight: 300;">CHILDREN</span></h3>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/Kooth-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>KOOTH</h3>
            <p>Kooth is an online mental wellbeing community. It gives teenagers and young adults access to free, safe
              and anonymous support.</p>
          </div>
        </div>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/relate-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>AVON RELATE</h3>
            <p>Relate also provides support and counselling to children, young people, and young adults. </p>
          </div>
        </div>
        <div class="help-block">
          <div class="help-img-wrapper"><img src="/wp-content/uploads/2023/05/young-minds-logo-t.webp" /></div>
          <div class="help-block-inner">
            <h3>YOUNGMINDS</h3>
            <p>YoungMinds helps teenagers and young adults to find help and support on a range of topics across the
              mental health spectrum. </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleSection( section = `help1` ) {
    for ( let i = 1; i <= 4; i++ ) {
      let btn = document.getElementById( `helpbtn${ i }` );
      let info = document.getElementById( `help${ i }info` );

      info.style.display = "none";
      btn.classList.remove( `helpBtnActive` );

      if ( section === `help${ i }` ) {
        btn.classList.add( `helpBtnActive` );
        info.style.display = "block";
      }
    }
  }
  window.addEventListener( `load`, function () {
    toggleSection( `help1` );
  } )

</script>
';

return $output;
}
add_shortcode('divorce_help', 'divorce_help_shortcode');
