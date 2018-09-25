<?php

/**
 * Answer
 *
 * @Table(name="game_answer")
 * @Entity
 */
class Answer {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var integer
     *
     * @Column(name="point", type="integer", nullable=false)
     */
    private $point;

    /**
     * @var integer
     *
     * @Column(name="used", type="integer", nullable=false)
     */
    private $used;

    /**
     * @var \Question
     *
     * @ManyToOne(targetEntity="Question")
     * @JoinColumns({
     *   @JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    private $question;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Answer
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Set point
     *
     * @param integer $point
     * @return Answer
     */
    public function setPoint($point) {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return integer 
     */
    public function getPoint() {
        return $this->point;
    }

    /**
     * Set used
     *
     * @param integer $used
     * @return Answer
     */
    public function setUsed($used) {
        $this->used = $used;

        return $this;
    }

    /**
     * Get used
     *
     * @return integer 
     */
    public function getUsed() {
        return $this->used;
    }

    /**
     * Set question
     *
     * @param \Question $question
     * @return Answer
     */
    public function setQuestion(\Question $question = null) {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Question 
     */
    public function getQuestion() {
        return $this->question;
    }

}

/**
 * Battle
 *
 * @Table(name="game_battle")
 * @Entity
 */
class Battle {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \User
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_one", referencedColumnName="id")
     * })
     */
    private $userOne;

    /**
     * @var \User
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_two", referencedColumnName="id")
     * })
     */
    private $userTwo;

    /**
     * @var \User
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_turn", referencedColumnName="id")
     * })
     */
    private $userTurn;

    /**
     * @var \User
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="winner", referencedColumnName="id")
     * })
     */
    private $winner;

    /**
     * @var \DateTime
     *
     * @Column(name="last_game_datetime", type="datetime", nullable=false)
     */
    private $lastGameDatetime;

    /**
     * @var boolean
     *
     * @Column(name="approved_user_one", type="boolean", nullable=false)
     */
    private $approvedUserOne = false;

    /**
     * @var boolean
     *
     * @Column(name="approved_user_two", type="boolean", nullable=false)
     */
    private $approvedUserTwo = false;

    /**
     * @var \DateTime
     *
     * @Column(name="creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @OneToMany(targetEntity="Round", mappedBy="battle")
     */
    private $rounds;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set userOne
     *
     * @param \User $userOne
     * @return Battle
     */
    public function setUserOne(\User $userOne = null) {
        $this->userOne = $userOne;

        return $this;
    }

    /**
     * Get userOne
     *
     * @return \User 
     */
    public function getUserOne() {
        return $this->userOne;
    }

    /**
     * Set userTwo
     *
     * @param \User $userTwo
     * @return Battle
     */
    public function setUserTwo(\User $userTwo = null) {
        $this->userTwo = $userTwo;

        return $this;
    }

    /**
     * Get userTwo
     *
     * @return \User 
     */
    public function getUserTwo() {
        return $this->userTwo;
    }

    /**
     * Set userTurn
     *
     * @param \User $userTurn
     * @return Battle
     */
    public function setUserTurn(\User $userTurn = null) {
        $this->userTurn = $userTurn;

        return $this;
    }

    /**
     * Get userTurn
     *
     * @return \User 
     */
    public function getUserTurn() {
        return $this->userTurn;
    }

    /**
     * Set winner
     *
     * @param \User $winner
     * @return Battle
     */
    public function setWinner(\User $winner = null) {
        $this->winner = $winner;

        return $this;
    }

    /**
     * Get winner
     *
     * @return \User 
     */
    public function getWinner() {
        return $this->winner;
    }

    /**
     * Set lastGameDatetime
     *
     * @param \DateTime $lastGameDatetime
     * @return Battle
     */
    public function setLastGameDatetime($lastGameDatetime) {
        $this->lastGameDatetime = $lastGameDatetime;

        return $this;
    }

    /**
     * Get lastGameDatetime
     *
     * @return \DateTime 
     */
    public function getLastGameDatetime() {
        return $this->lastGameDatetime;
    }

    /**
     * Set approvedUserOne
     *
     * @param boolean $approvedUserOne
     * @return Battle
     */
    public function setApprovedUserOne($approvedUserOne) {
        $this->approvedUserOne = $approvedUserOne;

        return $this;
    }

    /**
     * Get approvedUserOne
     *
     * @return boolean 
     */
    public function getApprovedUserOne() {
        return $this->approvedUserOne;
    }

    /**
     * Set approvedUserTwo
     *
     * @param boolean $approvedUserTwo
     * @return Battle
     */
    public function setApprovedUserTwo($approvedUserTwo) {
        $this->approvedUserTwo = $approvedUserTwo;

        return $this;
    }

    /**
     * Get approvedUserTwo
     *
     * @return boolean 
     */
    public function getApprovedUserTwo() {
        return $this->approvedUserTwo;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Battle
     */
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    public function setRounds($rounds) {
        $this->rounds = $rounds;
    }

    public function getRounds() {
        return $this->rounds;
    }

}

/**
 * CategorySuggest
 *
 * @Table(name="game_answer_suggest")
 * @Entity
 */
class AnswerSuggest {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="text", type="string", length=200, nullable=false)
     */
    private $text;

    /**
     * @var boolean
     *
     * @Column(name="approved", type="boolean", nullable=false)
     */
    private $approved = false;

    /**
     * @var \User
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Round
     *
     * @ManyToOne(targetEntity="Round")
     * @JoinColumns({
     *   @JoinColumn(name="round_id", referencedColumnName="id")
     * })
     */
    private $round;

    /**
     * @var \Question
     *
     * @ManyToOne(targetEntity="Question")
     * @JoinColumns({
     *   @JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    private $question;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return AnswerSuggest
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     * @return AnswerSuggest
     */
    public function setApproved($approved) {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean 
     */
    public function getApproved() {
        return $this->approved;
    }

    /**
     * Set user
     *
     * @param \User $user
     * @return AnswerSuggest
     */
    public function setUser(\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set round
     *
     * @param \Round $round
     * @return AnswerSuggest
     */
    public function setRound(\Round $round = null) {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round
     *
     * @return \Round 
     */
    public function getRound() {
        return $this->round;
    }

    /**
     * Set question
     *
     * @param \Question $question
     * @return AnswerSuggest
     */
    public function setQuestion(\Question $question = null) {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Question 
     */
    public function getQuestion() {
        return $this->question;
    }

}

/**
 * CategorySuggest
 *
 * @Table(name="game_category_suggest")
 * @Entity
 */
class CategorySuggest {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="name", type="string", length=200, nullable=false)
     */
    private $name;

    /**
     * @var \User
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CategorySuggest
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set user
     *
     * @param \User $user
     * @return CategorySuggest
     */
    public function setUser(\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \User 
     */
    public function getUser() {
        return $this->user;
    }

}

/**
 * Category
 *
 * @Table(name="game_category")
 * @Entity
 */
class Category {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="name", type="string", length=200, nullable=false)
     */
    private $name;

    /**
     * @OneToMany(targetEntity="Question", mappedBy="category")
     */
    private $questions;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    public function setQuestions($questions) {
        $this->questions = $questions;
    }

    public function getQuestions() {
        return $this->questions;
    }

}

/**
 * Question
 *
 * @Table(name="game_question")
 * @Entity
 */
class Question {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @Column(name="condition_query", type="string", length=200, nullable=true)
     */
    private $conditionQuery;

    /**
     * @var \Category
     *
     * @ManyToOne(targetEntity="Category")
     * @JoinColumns({
     *   @JoinColumn(name="category_id", referencedColumnName="id")
     * })
     */
    private $category;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Question
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Set conditionQuery
     *
     * @param string $conditionQuery
     * @return Question
     */
    public function setConditionQuery($conditionQuery) {
        $this->conditionQuery = $conditionQuery;

        return $this;
    }

    /**
     * Get conditionQuery
     *
     * @return string 
     */
    public function getConditionQuery() {
        return $this->conditionQuery;
    }

    /**
     * Set category
     *
     * @param \Category $category
     * @return Question
     */
    public function setCategory(\Category $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Category 
     */
    public function getCategory() {
        return $this->category;
    }

}

/**
 * Round
 *
 * @Table(name="game_round")
 * @Entity
 */
class Round {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="letter", type="string", length=1, nullable=false)
     */
    private $letter;

    /**
     * @var \Question
     *
     * @ManyToOne(targetEntity="Question")
     * @JoinColumns({
     *   @JoinColumn(name="question_one", referencedColumnName="id")
     * })
     */
    private $questionOne;

    /**
     * @var \Question
     *
     * @ManyToOne(targetEntity="Question")
     * @JoinColumns({
     *   @JoinColumn(name="question_two", referencedColumnName="id")
     * })
     */
    private $questionTwo;

    /**
     * @var \Question
     *
     * @ManyToOne(targetEntity="Question")
     * @JoinColumns({
     *   @JoinColumn(name="question_three", referencedColumnName="id")
     * })
     */
    private $questionThree;

    /**
     * @var \Question
     *
     * @ManyToOne(targetEntity="Question")
     * @JoinColumns({
     *   @JoinColumn(name="question_four", referencedColumnName="id")
     * })
     */
    private $questionFour;

    /**
     * @var \Question
     *
     * @ManyToOne(targetEntity="Question")
     * @JoinColumns({
     *   @JoinColumn(name="question_five", referencedColumnName="id")
     * })
     */
    private $questionFive;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_one_user_one", referencedColumnName="id")
     * })
     */
    private $answerOneUserOne;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_two_user_one", referencedColumnName="id")
     * })
     */
    private $answerTwoUserOne;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_three_user_one", referencedColumnName="id")
     * })
     */
    private $answerThreeUserOne;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_four_user_one", referencedColumnName="id")
     * })
     */
    private $answerFourUserOne;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_five_user_one", referencedColumnName="id")
     * })
     */
    private $answerFiveUserOne;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_one_user_two", referencedColumnName="id")
     * })
     */
    private $answerOneUserTwo;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_two_user_two", referencedColumnName="id")
     * })
     */
    private $answerTwoUserTwo;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_three_user_two", referencedColumnName="id")
     * })
     */
    private $answerThreeUserTwo;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_four_user_two", referencedColumnName="id")
     * })
     */
    private $answerFourUserTwo;

    /**
     * @var \Answer
     *
     * @ManyToOne(targetEntity="Answer")
     * @JoinColumns({
     *   @JoinColumn(name="answer_five_user_two", referencedColumnName="id")
     * })
     */
    private $answerFiveUserTwo;

    /**
     * @var string
     *
     * @Column(name="answer_text_one_user_one", type="string", length=200, nullable=true)
     */
    private $answerTextOneUserOne;

    /**
     * @var string
     *
     * @Column(name="answer_text_two_user_one", type="string", length=200, nullable=true)
     */
    private $answerTextTwoUserOne;

    /**
     * @var string
     *
     * @Column(name="answer_text_three_user_one", type="string", length=200, nullable=true)
     */
    private $answerTextThreeUserOne;

    /**
     * @var string
     *
     * @Column(name="answer_text_four_user_one", type="string", length=200, nullable=true)
     */
    private $answerTextFourUserOne;

    /**
     * @var string
     *
     * @Column(name="answer_text_five_user_one", type="string", length=200, nullable=true)
     */
    private $answerTextFiveUserOne;

    /**
     * @var string
     *
     * @Column(name="answer_text_one_user_two", type="string", length=200, nullable=true)
     */
    private $answerTextOneUserTwo;

    /**
     * @var string
     *
     * @Column(name="answer_text_two_user_two", type="string", length=200, nullable=true)
     */
    private $answerTextTwoUserTwo;

    /**
     * @var string
     *
     * @Column(name="answer_text_three_user_two", type="string", length=200, nullable=true)
     */
    private $answerTextThreeUserTwo;

    /**
     * @var string
     *
     * @Column(name="answer_text_four_user_two", type="string", length=200, nullable=true)
     */
    private $answerTextFourUserTwo;

    /**
     * @var string
     *
     * @Column(name="answer_text_five_user_two", type="string", length=200, nullable=true)
     */
    private $answerTextFiveUserTwo;

    /**
     * @var integer
     *
     * @Column(name="points_answer_one_user_one", type="integer", nullable=false)
     */
    private $pointsAnswerOneUserOne = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_two_user_one", type="integer", nullable=false)
     */
    private $pointsAnswerTwoUserOne = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_three_user_one", type="integer", nullable=false)
     */
    private $pointsAnswerThreeUserOne = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_four_user_one", type="integer", nullable=false)
     */
    private $pointsAnswerFourUserOne = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_five_user_one", type="integer", nullable=false)
     */
    private $pointsAnswerFiveUserOne = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_one_user_two", type="integer", nullable=false)
     */
    private $pointsAnswerOneUserTwo = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_two_user_two", type="integer", nullable=false)
     */
    private $pointsAnswerTwoUserTwo = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_three_user_two", type="integer", nullable=false)
     */
    private $pointsAnswerThreeUserTwo = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_four_user_two", type="integer", nullable=false)
     */
    private $pointsAnswerFourUserTwo = 0;

    /**
     * @var integer
     *
     * @Column(name="points_answer_five_user_two", type="integer", nullable=false)
     */
    private $pointsAnswerFiveUserTwo = 0;

    /**
     * @var integer
     *
     * @Column(name="points_user_one", type="integer", nullable=false)
     */
    private $pointsUserOne = 0;

    /**
     * @var integer
     *
     * @Column(name="points_user_two", type="integer", nullable=false)
     */
    private $pointsUserTwo = 0;

    /**
     * @var \DateTime
     *
     * @Column(name="started_datetime_user_one", type="datetime", nullable=true)
     */
    private $startedDatetimeUserOne;

    /**
     * @var \DateTime
     *
     * @Column(name="started_datetime_user_two", type="datetime", nullable=true)
     */
    private $startedDatetimeUserTwo;

    /**
     * @var \Battle
     *
     * @ManyToOne(targetEntity="Battle")
     * @JoinColumns({
     *   @JoinColumn(name="battle_id", referencedColumnName="id")
     * })
     */
    private $battle;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set letter
     *
     * @param string $letter
     * @return Round
     */
    public function setLetter($letter) {
        $this->letter = $letter;

        return $this;
    }

    /**
     * Get letter
     *
     * @return string 
     */
    public function getLetter() {
        return $this->letter;
    }

    /**
     * Set questionOne
     *
     * @param \Question $questionOne
     * @return Round
     */
    public function setQuestionOne(\Question $questionOne = null) {
        $this->questionOne = $questionOne;

        return $this;
    }

    /**
     * Get questionOne
     *
     * @return \Question 
     */
    public function getQuestionOne() {
        return $this->questionOne;
    }

    /**
     * Set questionTwo
     *
     * @param \Question $questionTwo
     * @return Round
     */
    public function setQuestionTwo(\Question $questionTwo = null) {
        $this->questionTwo = $questionTwo;

        return $this;
    }

    /**
     * Get questionTwo
     *
     * @return \Question 
     */
    public function getQuestionTwo() {
        return $this->questionTwo;
    }

    /**
     * Set questionThree
     *
     * @param \Question $questionThree
     * @return Round
     */
    public function setQuestionThree(\Question $questionThree = null) {
        $this->questionThree = $questionThree;

        return $this;
    }

    /**
     * Get questionThree
     *
     * @return \Question 
     */
    public function getQuestionThree() {
        return $this->questionThree;
    }

    /**
     * Set questionFour
     *
     * @param \Question $questionFour
     * @return Round
     */
    public function setQuestionFour(\Question $questionFour = null) {
        $this->questionFour = $questionFour;

        return $this;
    }

    /**
     * Get questionFour
     *
     * @return \Question 
     */
    public function getQuestionFour() {
        return $this->questionFour;
    }

    /**
     * Set questionFive
     *
     * @param \Question $questionFive
     * @return Round
     */
    public function setQuestionFive(\Question $questionFive = null) {
        $this->questionFive = $questionFive;

        return $this;
    }

    /**
     * Get questionFive
     *
     * @return \Question 
     */
    public function getQuestionFive() {
        return $this->questionFive;
    }

    /**
     * Set answerOneUserOne
     *
     * @param \Answer $answerOneUserOne
     * @return Round
     */
    public function setAnswerOneUserOne(\Answer $answerOneUserOne = null) {
        $this->answerOneUserOne = $answerOneUserOne;

        return $this;
    }

    /**
     * Get answerOneUserOne
     *
     * @return \Answer 
     */
    public function getAnswerOneUserOne() {
        return $this->answerOneUserOne;
    }

    /**
     * Set answerTwoUserOne
     *
     * @param \Answer $answerTwoUserOne
     * @return Round
     */
    public function setAnswerTwoUserOne(\Answer $answerTwoUserOne = null) {
        $this->answerTwoUserOne = $answerTwoUserOne;

        return $this;
    }

    /**
     * Get answerTwoUserOne
     *
     * @return \Answer 
     */
    public function getAnswerTwoUserOne() {
        return $this->answerTwoUserOne;
    }

    /**
     * Set answerThreeUserOne
     *
     * @param \Answer $answerThreeUserOne
     * @return Round
     */
    public function setAnswerThreeUserOne(\Answer $answerThreeUserOne = null) {
        $this->answerThreeUserOne = $answerThreeUserOne;

        return $this;
    }

    /**
     * Get answerThreeUserOne
     *
     * @return \Answer 
     */
    public function getAnswerThreeUserOne() {
        return $this->answerThreeUserOne;
    }

    /**
     * Set answerFourUserOne
     *
     * @param \Answer $answerFourUserOne
     * @return Round
     */
    public function setAnswerFourUserOne(\Answer $answerFourUserOne = null) {
        $this->answerFourUserOne = $answerFourUserOne;

        return $this;
    }

    /**
     * Get answerFourUserOne
     *
     * @return \Answer 
     */
    public function getAnswerFourUserOne() {
        return $this->answerFourUserOne;
    }

    /**
     * Set answerFiveUserOne
     *
     * @param \Answer $answerFiveUserOne
     * @return Round
     */
    public function setAnswerFiveUserOne(\Answer $answerFiveUserOne = null) {
        $this->answerFiveUserOne = $answerFiveUserOne;

        return $this;
    }

    /**
     * Set answerOneUserTwo
     *
     * @param \Answer $answerOneUserTwo
     * @return Round
     */
    public function setAnswerOneUserTwo(\Answer $answerOneUserTwo = null) {
        $this->answerOneUserTwo = $answerOneUserTwo;

        return $this;
    }

    /**
     * Get answerOneUserTwo
     *
     * @return \Answer 
     */
    public function getAnswerOneUserTwo() {
        return $this->answerOneUserTwo;
    }

    /**
     * Set answerTwoUserTwo
     *
     * @param \Answer $answerTwoUserTwo
     * @return Round
     */
    public function setAnswerTwoUserTwo(\Answer $answerTwoUserTwo = null) {
        $this->answerTwoUserTwo = $answerTwoUserTwo;

        return $this;
    }

    /**
     * Get answerTwoUserTwo
     *
     * @return \Answer 
     */
    public function getAnswerTwoUserTwo() {
        return $this->answerTwoUserTwo;
    }

    /**
     * Set answerThreeUserTwo
     *
     * @param \Answer $answerThreeUserTwo
     * @return Round
     */
    public function setAnswerThreeUserTwo(\Answer $answerThreeUserTwo = null) {
        $this->answerThreeUserTwo = $answerThreeUserTwo;

        return $this;
    }

    /**
     * Get answerThreeUserTwo
     *
     * @return \Answer 
     */
    public function getAnswerThreeUserTwo() {
        return $this->answerThreeUserTwo;
    }

    /**
     * Set answerFourUserTwo
     *
     * @param \Answer $answerFourUserTwo
     * @return Round
     */
    public function setAnswerFourUserTwo(\Answer $answerFourUserTwo = null) {
        $this->answerFourUserTwo = $answerFourUserTwo;

        return $this;
    }

    /**
     * Get answerFourUserTwo
     *
     * @return \Answer 
     */
    public function getAnswerFourUserTwo() {
        return $this->answerFourUserTwo;
    }

    /**
     * Set answerFiveUserTwo
     *
     * @param \Answer $answerFiveUserTwo
     * @return Round
     */
    public function setAnswerFiveUserTwo(\Answer $answerFiveUserTwo = null) {
        $this->answerFiveUserTwo = $answerFiveUserTwo;

        return $this;
    }

    /**
     * Get answerFiveUserTwo
     *
     * @return \Answer 
     */
    public function getAnswerFiveUserTwo() {
        return $this->answerFiveUserTwo;
    }

    /**
     * Get answerFiveUserOne
     *
     * @return \Answer 
     */
    public function getAnswerFiveUserOne() {
        return $this->answerFiveUserOne;
    }

    /**
     * Set answerTextOneUserOne
     *
     * @param string $answerTextOneUserOne
     * @return Round
     */
    public function setAnswerTextOneUserOne($answerTextOneUserOne) {
        $this->answerTextOneUserOne = $answerTextOneUserOne;

        return $this;
    }

    /**
     * Get answerTextOneUserOne
     *
     * @return string 
     */
    public function getAnswerTextOneUserOne() {
        return $this->answerTextOneUserOne;
    }

    /**
     * Set answerTextTwoUserOne
     *
     * @param string $answerTextTwoUserOne
     * @return Round
     */
    public function setAnswerTextTwoUserOne($answerTextTwoUserOne) {
        $this->answerTextTwoUserOne = $answerTextTwoUserOne;

        return $this;
    }

    /**
     * Get answerTextTwoUserOne
     *
     * @return string 
     */
    public function getAnswerTextTwoUserOne() {
        return $this->answerTextTwoUserOne;
    }

    /**
     * Set answerTextThreeUserOne
     *
     * @param string $answerTextThreeUserOne
     * @return Round
     */
    public function setAnswerTextThreeUserOne($answerTextThreeUserOne) {
        $this->answerTextThreeUserOne = $answerTextThreeUserOne;

        return $this;
    }

    /**
     * Get answerTextThreeUserOne
     *
     * @return string 
     */
    public function getAnswerTextThreeUserOne() {
        return $this->answerTextThreeUserOne;
    }

    /**
     * Set answerTextFourUserOne
     *
     * @param string $answerTextFourUserOne
     * @return Round
     */
    public function setAnswerTextFourUserOne($answerTextFourUserOne) {
        $this->answerTextFourUserOne = $answerTextFourUserOne;

        return $this;
    }

    /**
     * Get answerTextFourUserOne
     *
     * @return string 
     */
    public function getAnswerTextFourUserOne() {
        return $this->answerTextFourUserOne;
    }

    /**
     * Set answerTextFiveUserOne
     *
     * @param string $answerTextFiveUserOne
     * @return Round
     */
    public function setAnswerTextFiveUserOne($answerTextFiveUserOne) {
        $this->answerTextFiveUserOne = $answerTextFiveUserOne;

        return $this;
    }

    /**
     * Get answerTextFiveUserOne
     *
     * @return string 
     */
    public function getAnswerTextFiveUserOne() {
        return $this->answerTextFiveUserOne;
    }

    /**
     * Set answerTextOneUserTwo
     *
     * @param string $answerTextOneUserTwo
     * @return Round
     */
    public function setAnswerTextOneUserTwo($answerTextOneUserTwo) {
        $this->answerTextOneUserTwo = $answerTextOneUserTwo;

        return $this;
    }

    /**
     * Get answerTextOneUserTwo
     *
     * @return string 
     */
    public function getAnswerTextOneUserTwo() {
        return $this->answerTextOneUserTwo;
    }

    /**
     * Set answerTextTwoUserTwo
     *
     * @param string $answerTextTwoUserTwo
     * @return Round
     */
    public function setAnswerTextTwoUserTwo($answerTextTwoUserTwo) {
        $this->answerTextTwoUserTwo = $answerTextTwoUserTwo;

        return $this;
    }

    /**
     * Get answerTextTwoUserTwo
     *
     * @return string 
     */
    public function getAnswerTextTwoUserTwo() {
        return $this->answerTextTwoUserTwo;
    }

    /**
     * Set answerTextThreeUserTwo
     *
     * @param string $answerTextThreeUserTwo
     * @return Round
     */
    public function setAnswerTextThreeUserTwo($answerTextThreeUserTwo) {
        $this->answerTextThreeUserTwo = $answerTextThreeUserTwo;

        return $this;
    }

    /**
     * Get answerTextThreeUserTwo
     *
     * @return string 
     */
    public function getAnswerTextThreeUserTwo() {
        return $this->answerTextThreeUserTwo;
    }

    /**
     * Set answerTextFourUserTwo
     *
     * @param string $answerTextFourUserTwo
     * @return Round
     */
    public function setAnswerTextFourUserTwo($answerTextFourUserTwo) {
        $this->answerTextFourUserTwo = $answerTextFourUserTwo;

        return $this;
    }

    /**
     * Get answerTextFourUserTwo
     *
     * @return string 
     */
    public function getAnswerTextFourUserTwo() {
        return $this->answerTextFourUserTwo;
    }

    /**
     * Set answerTextFiveUserTwo
     *
     * @param string $answerTextFiveUserTwo
     * @return Round
     */
    public function setAnswerTextFiveUserTwo($answerTextFiveUserTwo) {
        $this->answerTextFiveUserTwo = $answerTextFiveUserTwo;

        return $this;
    }

    /**
     * Get answerTextFiveUserTwo
     *
     * @return string 
     */
    public function getAnswerTextFiveUserTwo() {
        return $this->answerTextFiveUserTwo;
    }

    /**
     * Set pointsAnswerOneUserOne
     *
     * @param integer $pointsAnswerOneUserOne
     * @return Round
     */
    public function setPointsAnswerOneUserOne($pointsAnswerOneUserOne) {
        $this->pointsAnswerOneUserOne = $pointsAnswerOneUserOne;

        return $this;
    }

    /**
     * Get pointsAnswerOneUserOne
     *
     * @return integer 
     */
    public function getPointsAnswerOneUserOne() {
        return $this->pointsAnswerOneUserOne;
    }

    /**
     * Set pointsAnswerTwoUserOne
     *
     * @param integer $pointsAnswerTwoUserOne
     * @return Round
     */
    public function setPointsAnswerTwoUserOne($pointsAnswerTwoUserOne) {
        $this->pointsAnswerTwoUserOne = $pointsAnswerTwoUserOne;

        return $this;
    }

    /**
     * Get pointsAnswerTwoUserOne
     *
     * @return integer 
     */
    public function getPointsAnswerTwoUserOne() {
        return $this->pointsAnswerTwoUserOne;
    }

    /**
     * Set pointsAnswerThreeUserOne
     *
     * @param integer $pointsAnswerThreeUserOne
     * @return Round
     */
    public function setPointsAnswerThreeUserOne($pointsAnswerThreeUserOne) {
        $this->pointsAnswerThreeUserOne = $pointsAnswerThreeUserOne;

        return $this;
    }

    /**
     * Get pointsAnswerThreeUserOne
     *
     * @return integer 
     */
    public function getPointsAnswerThreeUserOne() {
        return $this->pointsAnswerThreeUserOne;
    }

    /**
     * Set pointsAnswerFourUserOne
     *
     * @param integer $pointsAnswerFourUserOne
     * @return Round
     */
    public function setPointsAnswerFourUserOne($pointsAnswerFourUserOne) {
        $this->pointsAnswerFourUserOne = $pointsAnswerFourUserOne;

        return $this;
    }

    /**
     * Get pointsAnswerFourUserOne
     *
     * @return integer 
     */
    public function getPointsAnswerFourUserOne() {
        return $this->pointsAnswerFourUserOne;
    }

    /**
     * Set pointsAnswerFiveUserOne
     *
     * @param integer $pointsAnswerFiveUserOne
     * @return Round
     */
    public function setPointsAnswerFiveUserOne($pointsAnswerFiveUserOne) {
        $this->pointsAnswerFiveUserOne = $pointsAnswerFiveUserOne;

        return $this;
    }

    /**
     * Get pointsAnswerFiveUserOne
     *
     * @return integer 
     */
    public function getPointsAnswerFiveUserOne() {
        return $this->pointsAnswerFiveUserOne;
    }

    /**
     * Set pointsAnswerOneUserTwo
     *
     * @param integer $pointsAnswerOneUserTwo
     * @return Round
     */
    public function setPointsAnswerOneUserTwo($pointsAnswerOneUserTwo) {
        $this->pointsAnswerOneUserTwo = $pointsAnswerOneUserTwo;

        return $this;
    }

    /**
     * Get pointsAnswerOneUserTwo
     *
     * @return integer 
     */
    public function getPointsAnswerOneUserTwo() {
        return $this->pointsAnswerOneUserTwo;
    }

    /**
     * Set pointsAnswerTwoUserTwo
     *
     * @param integer $pointsAnswerTwoUserTwo
     * @return Round
     */
    public function setPointsAnswerTwoUserTwo($pointsAnswerTwoUserTwo) {
        $this->pointsAnswerTwoUserTwo = $pointsAnswerTwoUserTwo;

        return $this;
    }

    /**
     * Get pointsAnswerTwoUserTwo
     *
     * @return integer 
     */
    public function getPointsAnswerTwoUserTwo() {
        return $this->pointsAnswerTwoUserTwo;
    }

    /**
     * Set pointsAnswerThreeUserTwo
     *
     * @param integer $pointsAnswerThreeUserTwo
     * @return Round
     */
    public function setPointsAnswerThreeUserTwo($pointsAnswerThreeUserTwo) {
        $this->pointsAnswerThreeUserTwo = $pointsAnswerThreeUserTwo;

        return $this;
    }

    /**
     * Get pointsAnswerThreeUserTwo
     *
     * @return integer 
     */
    public function getPointsAnswerThreeUserTwo() {
        return $this->pointsAnswerThreeUserTwo;
    }

    /**
     * Set pointsAnswerFourUserTwo
     *
     * @param integer $pointsAnswerFourUserTwo
     * @return Round
     */
    public function setPointsAnswerFourUserTwo($pointsAnswerFourUserTwo) {
        $this->pointsAnswerFourUserTwo = $pointsAnswerFourUserTwo;

        return $this;
    }

    /**
     * Get pointsAnswerFourUserTwo
     *
     * @return integer 
     */
    public function getPointsAnswerFourUserTwo() {
        return $this->pointsAnswerFourUserTwo;
    }

    /**
     * Set pointsAnswerFiveUserTwo
     *
     * @param integer $pointsAnswerFiveUserTwo
     * @return Round
     */
    public function setPointsAnswerFiveUserTwo($pointsAnswerFiveUserTwo) {
        $this->pointsAnswerFiveUserTwo = $pointsAnswerFiveUserTwo;

        return $this;
    }

    /**
     * Get pointsAnswerFiveUserTwo
     *
     * @return integer 
     */
    public function getPointsAnswerFiveUserTwo() {
        return $this->pointsAnswerFiveUserTwo;
    }

    /**
     * Set pointsUserOne
     *
     * @param integer $pointsUserOne
     * @return Round
     */
    public function setPointsUserOne($pointsUserOne) {
        $this->pointsUserOne = $pointsUserOne;

        return $this;
    }

    /**
     * Get pointsUserOne
     *
     * @return integer 
     */
    public function getPointsUserOne() {
        return $this->pointsUserOne;
    }

    /**
     * Set pointsUserTwo
     *
     * @param integer $pointsUserTwo
     * @return Round
     */
    public function setPointsUserTwo($pointsUserTwo) {
        $this->pointsUserTwo = $pointsUserTwo;

        return $this;
    }

    /**
     * Get pointsUserTwo
     *
     * @return integer 
     */
    public function getPointsUserTwo() {
        return $this->pointsUserTwo;
    }

    /**
     * Set startedDatetimeUserOne
     *
     * @param \DateTime $startedDatetimeUserOne
     * @return Round
     */
    public function setStartedDatetimeUserOne($startedDatetimeUserOne) {
        $this->startedDatetimeUserOne = $startedDatetimeUserOne;

        return $this;
    }

    /**
     * Get startedDatetimeUserOne
     *
     * @return \DateTime 
     */
    public function getStartedDatetimeUserOne() {
        return $this->startedDatetimeUserOne;
    }

    /**
     * Set startedDatetimeUserTwo
     *
     * @param \DateTime $startedDatetimeUserTwo
     * @return Round
     */
    public function setStartedDatetimeUserTwo($startedDatetimeUserTwo) {
        $this->startedDatetimeUserTwo = $startedDatetimeUserTwo;

        return $this;
    }

    /**
     * Get startedDatetimeUserTwo
     *
     * @return \DateTime 
     */
    public function getStartedDatetimeUserTwo() {
        return $this->startedDatetimeUserTwo;
    }

    /**
     * Set battle
     *
     * @param \Battle $battle
     * @return Round
     */
    public function setBattle(\Battle $battle = null) {
        $this->battle = $battle;

        return $this;
    }

    /**
     * Get battle
     *
     * @return \Battle 
     */
    public function getBattle() {
        return $this->battle;
    }

}

/**
 * User
 *
 * @Table(name="game_user")
 * @Entity
 */
class User {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @Column(name="last_name", type="string", length=200, nullable=true)
     */
    private $lastName = null;

    /**
     * @var string
     *
     * @Column(name="email", type="string", length=200, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @Column(name="password", type="text", nullable=true)
     */
    private $password;

    /**
     * @var integer
     *
     * @Column(name="coins", type="integer", nullable=false)
     */
    private $coins;

    /**
     * @var integer
     *
     * @Column(name="experience", type="integer", nullable=false)
     */
    private $experience;

    /**
     * @var integer
     *
     * @Column(name="victories", type="integer", nullable=false)
     */
    private $victories;

    /**
     * @var integer
     *
     * @Column(name="best_score", type="integer", nullable=true)
     */
    private $bestScore;

    /**
     * @var integer
     *
     * @Column(name="unbeaten", type="integer", nullable=false)
     */
    private $unbeaten = 0;

    /**
     * @var integer
     *
     * @Column(name="lives", type="integer", nullable=false)
     */
    private $lives;

    /**
     * @var \DateTime
     *
     * @Column(name="last_game_datetime", type="datetime", nullable=false)
     */
    private $lastGameDatetime;

    /**
     * @var string
     *
     * @Column(name="image", type="text", nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @Column(name="cover", type="text", nullable=true)
     */
    private $cover;

    /**
     * @var string
     *
     * @Column(name="user_id_fb", type="text", nullable=true)
     */
    private $userIdFb;

    /**
     * @var boolean
     *
     * @Column(name="is_premium", type="boolean", nullable=false)
     */
    private $isPremium = false;

    /**
     * @var string
     *
     * @Column(name="code_password", type="text", nullable=true)
     */
    private $codePassword;

    /**
     * @var \DateTime
     *
     * @Column(name="request_password", type="datetime", nullable=true)
     */
    private $requestPassword;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set coins
     *
     * @param integer $coins
     * @return User
     */
    public function setCoins($coins) {
        $this->coins = $coins;

        return $this;
    }

    /**
     * Get coins
     *
     * @return integer 
     */
    public function getCoins() {
        return $this->coins;
    }

    /**
     * Set experience
     *
     * @param integer $experience
     * @return User
     */
    public function setExperience($experience) {
        $this->experience = $experience;

        return $this;
    }

    /**
     * Get experience
     *
     * @return integer 
     */
    public function getExperience() {
        return $this->experience;
    }

    /**
     * Set victories
     *
     * @param integer $victories
     * @return User
     */
    public function setVictories($victories) {
        $this->victories = $victories;

        return $this;
    }

    /**
     * Get victories
     *
     * @return integer 
     */
    public function getVictories() {
        return $this->victories;
    }

    /**
     * Set bestScore
     *
     * @param integer $bestScore
     * @return User
     */
    public function setBestScore($bestScore) {
        $this->bestScore = $bestScore;

        return $this;
    }

    /**
     * Get bestScore
     *
     * @return integer 
     */
    public function getBestScore() {
        return $this->bestScore;
    }

    /**
     * Set unbeaten
     *
     * @param integer $unbeaten
     * @return User
     */
    public function setUnbeaten($unbeaten) {
        $this->unbeaten = $unbeaten;

        return $this;
    }

    /**
     * Get unbeaten
     *
     * @return integer 
     */
    public function getUnbeaten() {
        return $this->unbeaten;
    }

    /**
     * Set lives
     *
     * @param integer $lives
     * @return User
     */
    public function setLives($lives) {
        $this->lives = $lives;

        return $this;
    }

    /**
     * Get lives
     *
     * @return integer 
     */
    public function getLives() {
        return $this->lives;
    }

    /**
     * Set lastGameDatetime
     *
     * @param \DateTime $lastGameDatetime
     * @return User
     */
    public function setLastGameDatetime($lastGameDatetime) {
        $this->lastGameDatetime = $lastGameDatetime;

        return $this;
    }

    /**
     * Get lastGameDatetime
     *
     * @return \DateTime 
     */
    public function getLastGameDatetime() {
        return $this->lastGameDatetime;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return User
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return User
     */
    public function setCover($cover) {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string 
     */
    public function getCover() {
        return $this->cover;
    }

    /**
     * Set userIdFb
     *
     * @param string $userIdFb
     * @return User
     */
    public function setUserIdFb($userIdFb) {
        $this->userIdFb = $userIdFb;

        return $this;
    }

    /**
     * Get userIdFb
     *
     * @return string 
     */
    public function getUserIdFb() {
        return $this->userIdFb;
    }

    /**
     * Set isPremium
     *
     * @param boolean $isPremium
     * @return User
     */
    public function setIsPremium($isPremium) {
        $this->isPremium = $isPremium;

        return $this;
    }

    /**
     * Get isPremium
     *
     * @return boolean 
     */
    public function getIsPremium() {
        return $this->isPremium;
    }

    /**
     * Set codePassword
     *
     * @param string $codePassword
     * @return User
     */
    public function setCodePassword($codePassword) {
        $this->codePassword = $codePassword;

        return $this;
    }

    /**
     * Get codePassword
     *
     * @return string 
     */
    public function getCodePassword() {
        return $this->codePassword;
    }

    /**
     * Set requestPassword
     *
     * @param \DateTime $requestPassword
     * @return User
     */
    public function setRequestPassword($requestPassword) {
        $this->requestPassword = $requestPassword;

        return $this;
    }

    /**
     * Get requestPassword
     *
     * @return \DateTime 
     */
    public function getRequestPassword() {
        return $this->requestPassword;
    }

}

/**
 * Payment
 *
 * @Table(name="game_payments")
 * @Entity
 */
class Payment {

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @Column(name="pack_id", type="integer", nullable=false)
     */
    private $packId;

    /**
     * @var string
     *
     * @Column(name="status", type="string", length=50, nullable=false)
     */
    private $status;

    /**
     * @var \User
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set packId
     *
     * @param integer $packId
     * @return GamePayments
     */
    public function setPackId($packId) {
        $this->packId = $packId;

        return $this;
    }

    /**
     * Get packId
     *
     * @return integer 
     */
    public function getPackId() {
        return $this->packId;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return GamePayments
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param \User $user
     * @return Payment
     */
    public function setUser(\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \User 
     */
    public function getUser() {
        return $this->user;
    }

}
