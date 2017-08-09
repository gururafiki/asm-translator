.386
mcrs MACRO 
	And eax,ebx
ENDM

mcr MACRO parm
	And eax,ebx
ENDM

data segment
	stri db "big ukrainian boss's string"
	hexa dw 3Eh
	bina dd 01101101b
	deca dd 21
data ends

cod1 segment
assume cs:cod1,ds:data
met2:
Adc ax,[ebx+eax+6]
Neg bh
Neg ebx

Dec ax
Adc ecx,[ebx+si+6]

Adc bx,bina
Adc ecx,stri

Cmp bx,ah
Cmp [ebx],eax

Cmp deca,ah
Cmp bin,eax
met1:

Or [eax+0110b],ebx
Or bh,bl
Or al,dh
Or cx,dx

Mov [bp+si+5],bl
Mov [eax+3Ah] ,bx


And [bx+di+5],8AF2h
And [ebx+6],8AF22E4Bh
Jc met1

And ecx,ebx

Jc near ptr met1
Jmp far ptr met3
cod1 ends

cod2 segment
assume cs:cod2,ds:data
met3:
Mov [bp+di+5],bl
Jmp far ptr met2
cod2 ends