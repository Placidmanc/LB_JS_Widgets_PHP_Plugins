<?php

function bonus_scheme_calculator_shortcode()
{
  $output = '<style>
  .gauge-container {
    position: absolute;
    width: 300px;
    height: 300px;
    transform: rotateY(180deg);
    z-index: 0;
  }

  .gauge-totals-container {
    position: absolute;
    top: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 300px;
    height: 300px;
    z-index: 1;
  }

  .gauge-totals-container p {
    font-size: 25px;
    font-weight: 900;
    color: #CC4877;
    text-align: center;
    letter-spacing: 1.25px;
    line-height: 23px;
  }

  .gauge-totals-container p span {
    font-size: 18px;
    letter-spacing: 0.95px;
  }

  #track,
  #addons,
  #house {
    position: absolute;
    top: 0;
    left: 0;
  }

  #track circle {
    fill: none;
  }

  #addons circle,
  #house circle {
    fill: none;
    stroke: #CC4877;
    stroke-dasharray: 827;
    stroke-dashoffset: 827;
    transition: stroke-dashoffset 0.3s linear;
    transform-origin: 50% 50%;
    transform: rotate(-90deg);
  }

  #house circle {
    stroke: #03B5B8;
    stroke-dashoffset: calc((1 - 0.142857) * 827);
  }

  /* buttons */
  .bonus-buttons-wrapper {
    display: flex;
    width: 324px;
    flex-wrap: wrap;
    gap: 15px;
  }

  .toggle-button {
    display: block;
    background-color: white;
    padding: 11px 0;
    width: 154px !important;
    border-radius: 15px;
    box-shadow: 0px 0px 16px 0px rgba(0, 0, 0, 0.15) !important;
    cursor: pointer;
    height: 46px !important;
    transition: all 0.15s ease-in-out;
    font-size: 20px;
    font-weight: 900;
    line-height: 24px;
    color: #03B5B8;
    text-align: center;
  }

  /* prevent sticky hover events on touch devices */
  @media (hover: hover) {
    .toggle-button:hover {
      background-color: #027779;
      color: #FFFFFF !important;
    }
  }

  /* active state */
  .addonButtonActive {
    background-color: #027779;
    color: #FFFFFF !important;
  }

  /* table */
  .table-container {
    width: 100%;
  }

  table.bonus-table th {
    font-size: 16px;
    font-weight: 900;
    color: #808080;
    text-align: center;
    line-height: 21px;
  }

  table.bonus-table td {
    font-size: 18px;
    font-weight: 500;
    line-height: 34px;
    color: #808080;
    text-align: center;
    vertical-align: middle;
  }

  table.bonus-table td.td-left {
    text-align: left;
  }

  table.bonus-table {
    width: 100%;
    empty-cells: show;
  }

  table.bonus-table .dot {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    display: flex;
    margin-right: 16px;
  }

  table.bonus-table .blue {
    background-color: #03B5B8;
  }

  table.bonus-table .pink {
    background-color: #CC4877;
  }
</style>

<div class="gauge-container">
  <svg id="track" width="300" height="300">
    <circle cx="150" cy="150" r="131.5" stroke-width="37" stroke-linecap="round" />
  </svg>
  <svg id="addons" width="300" height="300">
    <circle cx="150" cy="150" r="131.5" stroke-width="37" stroke-linecap="round" />
  </svg>
  <svg id="house" width="300" height="300">
    <circle cx="150" cy="150" r="131.5" stroke-width="37" stroke-linecap="round" />
  </svg>
</div>

<div class="gauge-totals-container">
  <p id="chartAgencyTotal">AGENCY<br><span>£100</span></p>
  <p id="chartAgentTotal">AGENT<br><span>£20</span></p>
</div>

<script>
  document.addEventListener( `DOMContentLoaded`, function () {
    const buttonStates = [ false, false, false, false, false, false ];
    const agencyHouseSale = 100;
    const agentHouseSale = 20;
    const agencyAddOn = 50;
    const agentAddOn = 20;
    const allButtons = document.getElementsByClassName( `toggle-button` );

    for ( let i = 0; i < allButtons.length; i++ ) {
      if ( window.innerWidth <= 1024 ) {
        // prevent sticky hover on touch devices
        allButtons[ i ].addEventListener( `touchend`, function () {
          event.preventDefault();
          updateGauge( i );
        } );
      } else {
        allButtons[ i ].addEventListener( `click`, function () {
          updateGauge( i );
        } );
      }
    }

    function updateGauge( index ) {
      const addons = document.querySelector( `#addons circle` );
      const maxOffset = 827; // 2 * PI * r (where r is 131.5)

      const chartAgencyTotalEl = document.getElementById( `chartAgencyTotal` );
      const chartAgentTotalEl = document.getElementById( `chartAgentTotal` );
      const tableAgencyAddOnsTotalEl = document.getElementById( `tableAgencyAddOnsTotal` );
      const tableAgentAddOnsTotalEl = document.getElementById( `tableAgentAddOnsTotal` );

      // set the new state
      buttonStates[ index ] = !buttonStates[ index ];

      if ( buttonStates[ index ] ) {
        allButtons[ index ].classList.add( `addonButtonActive` );
      } else {
        allButtons[ index ].classList.remove( `addonButtonActive` );
        // allButtons[ index ].blur();  // remove focus
      }

      // Calculate the table values based on number of add ons selected
      const totalOn = buttonStates.reduce( ( acc, state ) => acc + ( state ? 1 : 0 ), 0 );
      const value = totalOn * 0.142857 + 0.142857;
      const tableAgencyTotal = totalOn * agencyAddOn;
      const tableAgentTotal = totalOn * agentAddOn;
      const chartAgencyTotal = totalOn * agencyAddOn + agencyHouseSale;
      const chartAgentTotal = totalOn * agentAddOn + agentHouseSale;

      // Update the UI
      tableAgencyAddOnsTotalEl.innerHTML = `£${ tableAgencyTotal }`;
      tableAgentAddOnsTotalEl.innerHTML = `£${ tableAgentTotal }`;
      chartAgencyTotalEl.innerHTML = `AGENCY<br><span>£${ chartAgencyTotal }</span>`;
      chartAgentTotalEl.innerHTML = `AGENT<br><span>£${ chartAgentTotal }</span>`;

      // Calculate the new stroke-dashoffset for each value
      const newOffset1 = ( 1 - value ) * maxOffset;

      // Update the stroke-dashoffset
      addons.style.strokeDashoffset = newOffset1;
    }
  } );

</script>
';

  return $output;
}
add_shortcode('bonus_scheme_calculator', 'bonus_scheme_calculator_shortcode');

