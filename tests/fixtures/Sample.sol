pragma solidity ^0.4.18;

contract Sample {
    // structs
    struct Struct1 {
        address a;
        uint u;
        int i;
        bool b;
    }

    struct Struct2 {
        Struct1 s1;
        Struct1 s2;
    }

    // modifiers
    modifier modifier1() {
        require(true);
        _;
    }
    
    modifier modifier2() {
        _;
        require(true);
    }

    // base types
    address public a;
    uint public u;
    int public i;
    bool public b;
    Struct1 struct1;
    Struct2 struct2;
    
    // fix length arrays
    bytes2 public b2;
    bytes20 public b20;
    bytes20[2] public b202;
    address[4] public a4;
    uint[9] public u9;
    string[5] public s5;
    Struct1[4] struct14;

    // dynamic length arrays
    address[] public ad;
    uint[] public ud;
    int[][4] public id4;
    bytes y;
    string public s;
    Struct1[] structd;
    string[] ss;
    bytes20[] public bd20;
    
    // mapping
    mapping (uint => Struct1) public uintstruct1;

    // EVENTS
    event EventNoInputs();
    event Event1(uint u, int i, uint8[4] i84, address[2] a2, bool[3] b3);
    event Event2(uint u);
    event EventStruct(Struct1 s);
    event EventWithIndexedArgs(uint indexed u, string indexed s, string indexed slong, string s2);
    event BytesEvent(bytes20 b20);

    // METHODS
    // constructor
    function Sample(uint _u, int _i, bool _b) public {
        var _s = Struct1({
                a: 0x573fdB6CA93A63f69500011CC03441C8B66c7564,
                u: 0,
                i: 0,
                b: false
            });
        
        a = 0x4715db25f6B5e7C0124606bc22B97bb4a7dCc28a;
        u = _u;
        i = _i;
        b = _b;
        struct1 = _s;
        struct2 = Struct2({
            s1: _s,
            s2: _s
        });
        b2 = byte(1234567890);
        a4 = [
            0x573fdB6CA93A63f69500011CC03441C8B66c7564,
            0x573fdB6CA93A63f69500011CC03441C8B66c7564,
            0x573fdB6CA93A63f69500011CC03441C8B66c7564,
            0x573fdB6CA93A63f69500011CC03441C8B66c7564
        ];
        u9 = [1,2,3,4,5,6,7,8,9];
        s5 = ["one", "two", "three", "four", "five"];
        struct14[0] = _s;
        struct14[2] = _s;
        struct14[3] = _s;
        ad = [
            0x573fdB6CA93A63f69500011CC03441C8B66c7564,
            0x573fdB6CA93A63f69500011CC03441C8B66c7564
        ];
        ud = [10,20,30];
        id4[0] = [1,2,3,4];
        id4[1] = [10,20,30,40];
        id4[2] = [100,200,300,400];
        
        y.push(byte(0));
        y.push(byte(1));
        y.push(byte(2));
        s = "Numquam satis butyrum !";
        structd.push(_s);
        structd.push(_s);
        ss.push('Numquam');
        ss.push('satis');
        ss.push('butyrum');
        ss.push('!');
    }

    // with no inputs
    function pureNoInputNoOutput() public pure {}
    
    function pureNoInput() public pure returns(uint8 _x) {
        _x = 4;
    }
    
    function viewNoInput() public view returns(uint256 _u) {
        _u = u;
    }

    function noInputNoOutput() public {
        i = 5;
    }
    
    function payableNoInputNoOutput() public payable {
        i = 10;
    }

    function pureNoInputNoOutputWithModifier() public pure modifier1 {}
    
    function viewReturnStruct() public view returns(Struct1 _s, Struct1 _ms) {
        _s = struct14[1];
        _ms = Struct1({
          a: 0x573fdB6CA93A63f69500011CC03441C8B66c7564,
          u: 5,
          i: -9,
          b: true
      });
    }

    function getBytes() public view returns (bytes _y) {
        _y = y;
    }

    function getString() public view returns (string _s) {
        _s = s;
    }

    // with inputs
    function noOutput(address _a, uint _u, int _i, bool _b) public {
      struct14[1] = Struct1({
          a: _a,
          u: _u,
          i: _i,
          b: _b
      });
    }
    
    function pureWithIO(address _a) public pure returns(address _b) {
      _b = _a;
    }
    
    function io(uint _u, bool _b) public returns(int256 _i) {
        u = _u;
        b = _b;
        _i = i;
    }

    function setBytes(bytes _y) public {
        y = _y;
    }

    function setString(string _s) public {
        s = _s;
    }
    
    // complex
    
    function stringOutput() public view returns(string _return) {
        _return = s5[3];
    }
    
    function arrayInput(uint[9] _i9) public pure returns(uint _u0, uint _u1, uint _u2) {
        _u0 = _i9[0];
        _u1 = _i9[3];
        _u2 = _i9[8];
    }

    function readSS(uint _k) public view returns(string _s) {
        return ss[_k];
    }

    function setFixedBytes(bytes20 _b20, bytes20[2] _b202, bytes20[] _bd20) public {
        b20 = _b20;
        b202 = _b202;
        bd20 = _bd20;

        BytesEvent(_b20);
    }

    // polymorphism
    function polymorphic() public pure {
        
    }
    
    function polymorphic(uint _foo) public {
        u = _foo;
    }
    
    function polymorphic(bool _foo) public payable {
        if (_foo) {
            u = 0;
        }
    }

    // emitting events
    function emittingAnEvent() public {
        Event2(12345);
    }

    function emittingAnotherEvent() public {
        Event1(1, -2, [1,2,3,4], [0x573fdB6CA93A63f69500011CC03441C8B66c7564, 0x7bAC4e5274e4BB248D23148B572181aA73272505], [true, false, true]);
    }

    function emittingTwoEvent() public {
        EventNoInputs();
        Event2(9999);
    }

    function emittingEventWithIndexedArgs() public {
        EventWithIndexedArgs(9876543210, "foo", "jesuisunesuperlonguechainedecaractereetjeprendplus de 100 bits YOLO", "foo");
    }

    function emittingStructEvent() public {
        EventStruct(Struct1({
          a: 0x573fdB6CA93A63f69500011CC03441C8B66c7564,
          u: 5,
          i: -9,
          b: true
      }));
    }

    function() public payable{
    }
}
