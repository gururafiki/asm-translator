.386
mcrs MACRO
	And parm,dd
ENDM

data segment
str db "big ukrainian boss's string"
hex dw 3Eh
bina dd 01101101b
deca dd 21
data ends
cod1 segment
assume cs:cod1,ds:data
Neg bh
Neg ebx

Dec deca

Adc bh, ptr [ebx+6]
Adc ecx,[ebx+6]

Adc bh,bina
Adc ecx,hexa

Cmp es:[ebx+6],ah
Cmp [ebx+6],eax

Cmp deca,ah
Cmp bina,eax

Or eax,bh
Or bh,bl
Or al,edx
Or ecx,edx

Mov eax,bl
Mov eax ,bx
Mov eax,ebx
Mov al,bl
Mov al,bx
Mov al,ebx

met1:
;And [bx+si],8Ah
And [bx+bp+di+5],8AF2h
And [ebx+6],8AF22E4Bh

And hexa,bh
And bina,bx
And deca,ebx

Jc met1
Jmp met2
cod1 ends

cod2 segment
assume cs:cod2,ds:data
met2:
cod2 ends

END