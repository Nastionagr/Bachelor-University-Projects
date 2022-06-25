#define _CRT_SECURE_NO_WARNINGS
#include <stdio.h>
#include <stdlib.h>

#define SNP 12 //sizeof(header)
#define S 4 //sizeof(aheader)

void *POLE; //beginning of the array

typedef struct header //header for the free block
{
	int size; //size of the free block (int = 4)
	int next; //offset to the next free block (int = 4)
	int prev; //offset to the previous free block (int = 4)
} header;

typedef struct aheader //header for the allocated block
{
	int size; //size of the allocated block (int=4)
} aheader;

int best_fit(header **str, int size)
{
	int min_size = 0;
	header *t_str; //temporary pointer
	t_str = *str;

	while (t_str->next != 0) //checking the whole array of free blocks (despite the last one)
	{
		if (size <= t_str->size) //appropriate free block exists
		{
			//looking for the min free block, which size is >= necessary size
			if (min_size > t_str->size || min_size == 0) //if the previuos block is bigger - we remember the current one, so the 'wasting' of memory will be smaller
			{
				min_size = t_str->size; *str = t_str;
			}
		}

		(char*)t_str += t_str->next;
	}

	if (t_str->next == 0 && size <= t_str->size)
	{
		if (min_size == 0)
		{
			min_size = t_str->size; *str = t_str;
		}
		else
		{
			if (min_size > t_str->size) //if the previuos block is bigger - we remember the current one, so the 'wasting' of memory will be smaller
			{
				min_size = t_str->size; *str = t_str;
			}
		}
	}

	if (min_size == 0) 
	{
		*str = NULL;
		return 0;
	}

	if (min_size - size >= SNP) return 1; //checking if we should divide the block
	else return 0;
}

void *memory_alloc(unsigned int size)
{
	int separate;
	header *ptr;
	aheader *pointer;

	if (*(int*)((int)POLE + 4) == 0) return (void *)pointer = NULL; //checking if we still have some free space inside of our array

	ptr = (header *)((char *)POLE + *(int *)((int)POLE + 4)); //pointer to the beginning of the free block's list

	size += S; //adding some space for a header to the future allocated part

	separate = best_fit(&ptr, size);

	if (ptr == NULL) return (void *)pointer = NULL;

	pointer = (aheader *)ptr; //beginning of the allocated block

	if (separate == 1)
	{
		header *new = (char*)ptr + size;

		new->size = ptr->size - size;
		if (ptr->next == 0) new->next = 0;
		else new->next = ptr->next - size;
		if (ptr->prev == 0) new->prev = 0;
		else new->prev = ptr->prev + size;

		//redirection nearby offsets, so they point at the new block

		if (ptr->prev != 0) *((char*)ptr - ptr->prev + 4) += size;
		else *((int *)((int)POLE + 4)) += size;

		if (ptr->next != 0) *((char*)ptr + ptr->next + 8) -= size;
	}
	else //we should allocate the whole block
	{
		size = ptr->size;

		if (ptr->prev != 0)
		{
			if (ptr->next != 0) *(int *)((char *)ptr - ptr->prev + 4) += ptr->next;
			else *(int *)((char*)ptr - ptr->prev + 4) = 0;
		}
		else //future allocated block was at the beginning 
		{
			if (ptr->next != 0) *((int *)((int)POLE + 4)) += ptr->next;
			else *((int *)((int)POLE + 4)) = 0;
		}

		if (ptr->next != 0)
		{
			if (ptr->prev != 0) *(int *)((char*)ptr + ptr->next + 8) += ptr->prev;
			else *(int *)((char*)ptr + ptr->next + 8) = 0;
		}
	}

	pointer->size = size * (-1);
	pointer = (void *)((int)pointer + 4); //moving pointer of the allocated block, so that it points at the space after header

	return pointer;
}

void find_place(void *ptr, header **prev, header **next)
{
	header *seeker;

	*prev = (header*)((int)POLE + 4); //at first we suppose that our block is located completely in the beginning - after the main header

	if (*(int*)((int)POLE + 4) != 0) //if somewhere exists free block - we are looking for its accomodation relatively our ptr
	{
		seeker = (header*)((char *)POLE + *(int *)((int)POLE + 4)); //the first free block in the list
		*next = seeker;
	}
	else
	{
		*next = NULL;
		return;
	}

	while (ptr > seeker && seeker->next != 0)
	{
		*prev = seeker;
		*next = (char*)seeker + seeker->next;
		(char*)seeker += seeker->next;
	}

	if (seeker->next == 0 && ptr > seeker)
	{
		*prev = seeker;
		*next = NULL;
	}

	return;
}

int memory_free(void *valid_ptr)
{
	header *prev = NULL, *next; //pointer to the previous and next free blocks

	(char*)valid_ptr -= S; //pointer comes back to the header of the allocated block
	*((int *)valid_ptr) *= (-1); //we change the size of the allocated block to the positive number

	find_place(valid_ptr, &prev, &next);

	if (prev == NULL) return 1;

	if (prev == (header*)((int)POLE + 4))
	{
		if ((int *)valid_ptr - (int *)prev == 1) //valid_ptr is at the beginning
		{
			*(int *)((int)POLE + 4) = 8;
		}
		else //there is some allocated space at the beginning
		{
			*(int *)((int)POLE + 4) = (char *)valid_ptr - (char *)prev + 4; //plus int (size of the whole array)
		}
		((header*)valid_ptr)->prev = 0;
	}
	else
	{
		if ((char *)valid_ptr - (char *)prev == prev->size) //valid_ptr is right after prev
		{
			prev->size += ((header *)valid_ptr)->size; //union of the blocks
			valid_ptr = prev;
		}
		else //there is some allocated space between valid_ptr and prev
		{
			((header*)valid_ptr)->prev = (char *)valid_ptr - (char *)prev;
			prev->next = ((header*)valid_ptr)->prev;
		}
	}

	if (next == NULL) ((header*)valid_ptr)->next = 0; //if valid_ptr is in the last free block
	else
	{
		if ((char *)next - (char *)valid_ptr == ((header*)valid_ptr)->size) //if next is right after valid_ptr
		{
			((header*)valid_ptr)->size += next->size; //union of the blocks

			if (next->next == 0) ((header*)valid_ptr)->next = 0;
			else //if next isn't the last free block
			{
				(char *)next += next->next;
				((header*)valid_ptr)->next = (char *)next - (char *)valid_ptr;
				next->prev = ((header*)valid_ptr)->next;
			}
		}
		else  //if there is some free space between valid_ptr and next
		{
			((header*)valid_ptr)->next = (char *)next - (char *)valid_ptr;
			next->prev = ((header*)valid_ptr)->next;
		}
	}

	return 0;
}

void memory_init(void *ptr, unsigned int size)
{
	(char *)POLE = (char *)ptr;
	*(int *)POLE = size; //size of the whole array
	*(int *)((int)POLE + 4) = 8; //offset to the first free block

	header* block = (header *)((int)POLE + 8); //first empty block of memory
	block->size = size - 8;
	block->next = 0;
	block->prev = 0;
}

int memory_check(void *ptr)
{
	if ((header *)ptr >= (char*)POLE + 8 && (header *)ptr < (char*)POLE + *(int *)POLE) //checking if the pointer belongs to our array
	{
		int size, allocated;
		header *block;
		block = (char *)POLE + 8; //the first block

		while (1)
		{
			size = block->size;

			if (block->size >= 0) allocated = 0;
			else
			{
				allocated = 1;
				size *= (-1);
			}

			if ((header *)ptr >= block && (header *)ptr < (char*)block + size) return allocated; //checking if our ptr belongs to the block

			(char*)block += size; //keep going to the next block
		}
	}

	return 0;
}