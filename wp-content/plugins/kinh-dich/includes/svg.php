<?php

function iching_render_hero_svg(): string {
    ob_start(); ?>
    <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute; width: 0; height: 0; overflow: hidden;" aria-hidden="true">
        <symbol id="ich-hero-symbol" viewBox="0 0 900 400">
            <defs>
                <radialGradient id="heroGrad" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stop-color="var(--lbv-color-2)" stop-opacity="0.15"/>
                    <stop offset="100%" stop-color="var(--lbv-color-2)" stop-opacity="0"/>
                </radialGradient>
                <filter id="glow">
                    <feGaussianBlur stdDeviation="3" result="blur"/>
                    <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
            </defs>
            <circle cx="450" cy="200" r="200" fill="url(#heroGrad)"/>
            <g class="ich-svg-orbit" style="transform-origin:450px 200px">
                <circle cx="450" cy="200" r="170" fill="none" stroke="var(--lbv-color-2)" stroke-width="0.6" stroke-dasharray="4 18" stroke-opacity="0.35"/>
                <circle cx="450" cy="30" r="4" fill="var(--lbv-color-2)" fill-opacity="0.6"/>
                <circle cx="450" cy="370" r="4" fill="var(--lbv-color-2)" fill-opacity="0.6"/>
                <circle cx="280" cy="200" r="4" fill="var(--lbv-color-2)" fill-opacity="0.6"/>
                <circle cx="620" cy="200" r="4" fill="var(--lbv-color-2)" fill-opacity="0.6"/>
            </g>
            <g class="ich-svg-orbit-rev" style="transform-origin:450px 200px">
                <circle cx="450" cy="200" r="120" fill="none" stroke="var(--lbv-color-1)" stroke-width="0.5" stroke-dasharray="2 12" stroke-opacity="0.28"/>
                <circle cx="450" cy="80" r="2.5" fill="var(--lbv-color-1)" fill-opacity="0.5"/>
                <circle cx="450" cy="320" r="2.5" fill="var(--lbv-color-1)" fill-opacity="0.5"/>
                <circle cx="330" cy="200" r="2.5" fill="var(--lbv-color-1)" fill-opacity="0.5"/>
                <circle cx="570" cy="200" r="2.5" fill="var(--lbv-color-1)" fill-opacity="0.5"/>
                <circle cx="365" cy="115" r="2.5" fill="var(--lbv-color-1)" fill-opacity="0.5"/>
                <circle cx="535" cy="115" r="2.5" fill="var(--lbv-color-1)" fill-opacity="0.5"/>
                <circle cx="365" cy="285" r="2.5" fill="var(--lbv-color-1)" fill-opacity="0.5"/>
                <circle cx="535" cy="285" r="2.5" fill="var(--lbv-color-1)" fill-opacity="0.5"/>
            </g>
            <circle class="ich-svg-pulse-ring" cx="450" cy="200" r="110" fill="none" stroke="var(--lbv-color-2)" stroke-width="1.5" stroke-opacity="0.5"/>
            <g class="ich-svg-float" style="transform-origin:450px 200px">
                <circle cx="450" cy="200" r="48" fill="none" stroke="var(--lbv-color-2)" stroke-width="1" stroke-opacity="0.45"/>
                <path d="M450,152 A48,48 0 0,1 450,248 A24,24 0 0,1 450,200 A24,24 0 0,0 450,152 Z" fill="var(--lbv-color-2)" fill-opacity="0.18"/>
                <path d="M450,152 A48,48 0 0,0 450,248 A24,24 0 0,0 450,200 A24,24 0 0,1 450,152 Z" fill="var(--lbv-color-2)" fill-opacity="0.06"/>
                <circle cx="450" cy="176" r="6" fill="var(--lbv-color-2)" fill-opacity="0.35"/>
                <circle cx="450" cy="224" r="6" fill="var(--lbv-color-2)" fill-opacity="0.15"/>
                <line x1="428" y1="136" x2="472" y2="136" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="428" y1="131" x2="472" y2="131" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="428" y1="126" x2="472" y2="126" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="428" y1="264" x2="445" y2="264" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="455" y1="264" x2="472" y2="264" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="428" y1="269" x2="445" y2="269" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="455" y1="269" x2="472" y2="269" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="428" y1="274" x2="445" y2="274" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="455" y1="274" x2="472" y2="274" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="513" y1="188" x2="513" y2="205" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5" transform="rotate(90,513,200)"/>
                <line x1="518" y1="188" x2="518" y2="205" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5" transform="rotate(90,513,200)"/>
                <line x1="506" y1="190" x2="520" y2="190" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="506" y1="196" x2="520" y2="196" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="506" y1="202" x2="514" y2="202" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="518" y1="202" x2="520" y2="202" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="380" y1="190" x2="394" y2="190" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="398" y1="190" x2="400" y2="190" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="380" y1="196" x2="394" y2="196" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="398" y1="196" x2="400" y2="196" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
                <line x1="380" y1="202" x2="394" y2="202" stroke="var(--lbv-color-2)" stroke-width="2.5" stroke-opacity="0.5"/>
            </g>
            <path d="M 200,200 A 250,250 0 0,1 700,200" fill="none" stroke="var(--lbv-color-2)" stroke-width="0.6" stroke-dasharray="3 20" stroke-opacity="0.2"/>
            <path d="M 250,350 A 220,220 0 0,0 650,50" fill="none" stroke="var(--lbv-color-1)" stroke-width="0.5" stroke-dasharray="2 16" stroke-opacity="0.18"/>
            <circle cx="180" cy="120" r="3" fill="var(--lbv-color-2)" fill-opacity="0.25"/>
            <circle cx="720" cy="80" r="2" fill="var(--lbv-color-2)" fill-opacity="0.2"/>
            <circle cx="760" cy="310" r="3" fill="var(--lbv-color-1)" fill-opacity="0.22"/>
            <circle cx="130" cy="290" r="2" fill="var(--lbv-color-1)" fill-opacity="0.2"/>
            <circle cx="840" cy="180" r="2" fill="var(--lbv-color-2)" fill-opacity="0.18"/>
            <circle cx="60" cy="220" r="2" fill="var(--lbv-color-2)" fill-opacity="0.15"/>
        </symbol>
    </svg>
    <?php return ob_get_clean();
}