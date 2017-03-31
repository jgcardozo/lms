@extends('layouts.app')

@section('title', 'Survey test')

@section('content')
    <div class="survey-popup grid--flex flex--align-center flex--just-center" style="display: flex;">
        <form method="post" action="{{ route('survey.store') }}">
            {{ csrf_field() }}

            <div class="survey-popup__step">
                <input type="hidden" name="q[1]" value="When it comes to generating leads online and growing your email list, what's the single biggest challenge you've struggled with?" />
                <p class="survey-popup__question"><strong>When it comes to generating leads online and growing your email list, what's the single biggest challenge you've struggled with?</strong> <em>(Please be as detailed and specific as possible)</em></p> <br />

                <div class="survey-popup__answers">
                    <textarea name="a[1]"></textarea>
                </div>
            </div>

            <div class="survey-popup__step">
                <input type="hidden" name="q[2]" value="If you had to choose just one, which of the following is the most important for you to gain more clarity on right now?" />
                <p class="survey-popup__question"><strong>If you had to choose just one, which of the following is the most important for you to gain more clarity on right now?</strong></p>

                <div class="survey-popup__answers">
                    <label class="radio-control">
                        <input type="radio" name="a[2]" value="New Market: I'm trying to figure what might be the best new market to pursue, or if a new market I have in mind is worth pursuing" />
                        <span><strong>New Market:</strong> I'm trying to figure what might be the best new market to pursue, or if a new market I have in mind is worth pursuing</span>
                    </label>

                    <label class="radio-control">
                        <input type="radio" name="a[2]" value="Existing Market: I'm trying to figure out what blindspots I might have about my existing market or what might be the biggest challenges people are facing." />
                        <span><strong>Existing Market:</strong> I'm trying to figure out what blindspots I might have about my existing market or what might be the biggest challenges people are facing.</span>
                    </label>

                    <label class="radio-control">
                        <input type="radio" name="a[2]" value="New Product: I'm trying to figure out what might be the best new product to pursue, or if the new product I have in mind is worth pursuing" />
                        <span><strong>New Product:</strong> I'm trying to figure out what might be the best new product to pursue, or if the new product I have in mind is worth pursuing</span>
                    </label>

                    <label class="radio-control">
                        <input type="radio" name="a[2]" value="Existing Product: I'm trying to figure out what might be missing in my existing product, or why people might not be buying my existing product" />
                        <span><strong>Existing Product:</strong> I'm trying to figure out what might be missing in my existing product, or why people might not be buying my existing product</span>
                    </label>
                </div>
            </div>

            <div class="survey-popup__step">
                <input type="hidden" name="q[3]" value="Which of the following represents the largest source of revenue in your business:" />
                <p class="survey-popup__question"><strong>Which of the following represents the largest source of revenue in your business:</strong></p>

                <div class="survey-popup__answers">
                    <label class="radio-control">
                        <input type="radio" name="a[3]" value="Selling Digital Products / Software" />
                        <span>Selling Digital Products / Software</span>
                    </label>

                    <label class="radio-control">
                        <input type="radio" name="a[3]" value="Selling Physical Products / E-Commerce" />
                        <span>Selling Physical Products / E-Commerce</span>
                    </label>

                    <label class="radio-control">
                        <input type="radio" name="a[3]" value="Selling Services / Consulting" />
                        <span>Selling Services / Consulting</span>
                    </label>
                </div>
            </div>

            <div class="survey-popup__step">
                <input type="hidden" name="q[4]" value="If we were to create a paid program on one of the following topics (and you had to choose just one), which of the following are you most likely to have signed up for if it was already available?" />
                <p class="survey-popup__question"><strong>Last Question: If we were to create a paid program on one of the following topics (and you had to choose just one), which of the following are you most likely to have signed up for if it was already available?</strong></p>

                <div class="survey-popup__answers">
                    <label class="radio-control">
                        <input type="radio" name="a[4]" value="Market Selection: How to Choose a Profitable Niche You Can Dominate" />
                        <span><strong>Market Selection:</strong> How to Choose a Profitable Niche You Can Dominate</span>
                    </label>

                    <label class="radio-control">
                        <input type="radio" name="a[4]" value="Proof & Credibility: How to Generate an Avalanche of Testimonials & Social Proof" />
                        <span><strong>Proof & Credibility:</strong> How to Generate an Avalanche of Testimonials & Social Proof</span>
                    </label>

                    <label class="radio-control">
                        <input type="radio" name="a[4]" value="Persuasion: The Ultimate Copywriting Masterclass: Core Fundamentals & Advanced Techniques" />
                        <span><strong>Persuasion:</strong> The Ultimate Copywriting Masterclass: Core Fundamentals & Advanced Techniques</span>
                    </label>

                    <label class="radio-control">
                        <input type="radio" name="a[4]" value="Honestly, none of the above topics really interest me enough to consider signing up." />
                        <span><strong>Honestly, none of the above</strong> topics really interest me enough to consider signing up.</span>
                    </label>
                </div>
            </div>

            <div class="survey-popup__step">
                <p class="survey-popup__question"><strong>Last Step: What's your best contact information?</strong></p>

                <div class="survey-popup__answers">
                    <label style="max-width: 35rem;">
                        <span>Name:</span>
                        <input type="text" name="name" />
                    </label>

                    <label style="max-width: 35rem;">
                        <span>Email:</span>
                        <input type="text" name="email" />
                    </label>

                    <label style="max-width: 35rem;">
                        <span>Phone (optional): <span class="survey-popup__answers__desc">Lastly, I may wish to follow up with a few people on the phone personally to better understand your situation. Would you be open to speaking on the phone for a few minutes on the condition that i promise not to sell you anything? If so, would you please leave your phone number below? Thank you!</span></span>
                        <input type="text" name="phone" />
                    </label>
                </div>
            </div>

            <input type="submit"/>

            <div class="survey-popup__navigation">
                <a href="#">Previous</a>
                <a href="#">Next</a>
            </div>
        </form>
    </div>
@endsection